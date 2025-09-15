<?php

declare(strict_types=1);

namespace Hdaklue\Actioncrumb\Support;

use Closure;
use Exception;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Hdaklue\Actioncrumb\Action;
use ReflectionClass;
use ReflectionMethod;

/**
 * WireAction - Execute Filament Actions through Actioncrumb components
 * 
 * This class provides a fluent API to execute Filament Actions from within
 * hdaklue/actioncrumb breadcrumb components. It bridges the gap between
 * static breadcrumb definitions and dynamic Livewire/Filament actions.
 * 
 * Example usage:
 * ```php
 * WireAction::make('create-document')
 *     ->label('Create Document')
 *     ->livewire($this)
 *     ->icon('heroicon-o-plus')
 *     ->visible(fn() => auth()->user()->can('create-documents'))
 *     ->execute('createDocument');
 * ```
 */
final class WireAction
{
    private string $id;

    private string|Closure|null $label = null;

    private ?string $icon = null;

    private ?HasActions $component = null;

    private ?string $actionName = null;

    private array $parameters = [];

    private bool $validated = true;

    private bool|Closure $visible = true;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Create a new WireAction.
     */
    public static function make(string $id): self
    {
        return new self($id);
    }

    /**
     * Create multiple WireActions at once.
     * 
     * @param HasActions $component The Livewire component with actions
     * @param array $actions Array of action configurations:
     *                      [
     *                          'id' => 'unique-action-id', // optional, defaults to 'action' value
     *                          'label' => 'Action Label',
     *                          'action' => 'actionMethodName',
     *                          'icon' => 'heroicon-o-star', // optional
     *                          'parameters' => [], // optional
     *                          'validated' => true, // optional
     *                          'visible' => true // optional, bool or Closure
     *                      ]
     * @return Action[] Array of hdaklue/actioncrumb Action instances
     */
    public static function bulk(HasActions $component, array $actions): array
    {
        $result = [];

        foreach ($actions as $config) {
            $id = $config['id'] ?? $config['action'];
            $label = $config['label'];
            $actionName = $config['action'];
            $icon = $config['icon'] ?? null;
            $parameters = $config['parameters'] ?? [];
            $validated = $config['validated'] ?? true;
            $visible = $config['visible'] ?? true;

            $wireAction = self::make($id)
                ->label($label)
                ->livewire($component)
                ->validated($validated)
                ->visible($visible);

            if ($icon) {
                $wireAction->icon($icon);
            }

            if (! empty($parameters)) {
                $wireAction->parameters($parameters);
            }

            $action = $wireAction->execute($actionName);
            
            // Only add to result if action is visible (not null)
            if ($action) {
                $result[] = $action;
            }
        }

        return $result;
    }

    /**
     * Get component info for debugging.
     * 
     * @param HasActions $component
     * @return array Information about available actions on the component
     */
    public static function debug(HasActions $component): array
    {
        $availableActions = [];
        $reflection = new ReflectionClass($component);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (str_ends_with($method->getName(), 'Action')) {
                $actionName = str_replace('Action', '', $method->getName());
                $availableActions[] = $actionName;
            }
        }

        return [
            'class' => get_class($component),
            'available_actions' => $availableActions,
        ];
    }

    /**
     * Set the Livewire component that contains the actions.
     */
    public function livewire(HasActions $component): self
    {
        $this->component = $component;

        return $this;
    }

    /**
     * Set the action label.
     */
    public function label(string|Closure $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set the action icon (Heroicon identifier).
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Set the action visibility.
     */
    public function visible(bool|Closure $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Set parameters to pass to the action.
     */
    public function parameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Enable/disable action validation.
     * 
     * When enabled, checks if the action method exists before execution.
     * Disable for dynamic actions or when using __call magic methods.
     */
    public function validated(bool $validated = true): self
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Execute the action and return a hdaklue/actioncrumb Action instance.
     * 
     * @param string $actionName The name of the action method (without 'Action' suffix)
     * @param array $parameters Additional parameters to merge
     * @return Action|null The actioncrumb Action instance or null if not visible
     */
    public function execute(string $actionName, array $parameters = []): ?Action
    {
        $this->actionName = $actionName;
        $this->parameters = array_merge($this->parameters, $parameters);

        // Check visibility first
        if (! $this->resolveVisible()) {
            return null;
        }

        $label = $this->resolveLabel();

        $action = Action::make($label)
            ->execute(fn () => $this->executeAction());

        if ($this->icon) {
            $action->icon($this->icon);
        }

        return $action;
    }

    /**
     * Execute the Filament action with validation and error handling.
     */
    private function executeAction(): bool
    {
        try {
            // Validate component is available
            if (! $this->component) {
                $this->notifyError(
                    'No Component',
                    'Livewire component is required to execute this action',
                );

                return false;
            }

            // Validate action exists if validation is enabled
            if ($this->validated && ! $this->hasAction()) {
                $this->notifyError(
                    'Action Not Found',
                    "Action '{$this->actionName}' does not exist on component",
                );

                return false;
            }

            // Log action execution (if Laravel logger is available)
            if (function_exists('logger')) {
                logger()->info('WireAction executing', [
                    'action_name' => $this->actionName,
                    'component_class' => get_class($this->component),
                    'parameters' => $this->parameters,
                ]);
            }

            // Execute the Filament action
            $this->component->mountAction($this->actionName, $this->parameters);

            // Log successful execution
            if (function_exists('logger')) {
                logger()->info('WireAction executed successfully', [
                    'action_name' => $this->actionName,
                ]);
            }

            return true;

        } catch (Exception $e) {
            // Log error
            if (function_exists('logger')) {
                logger()->error('WireAction execution failed', [
                    'action_name' => $this->actionName,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            $this->notifyError(
                'Action Failed',
                "Failed to execute '{$this->actionName}': " . $e->getMessage(),
            );

            return false;
        }
    }

    /**
     * Check if the component has the required action method.
     */
    private function hasAction(): bool
    {
        if (! $this->component || ! $this->actionName) {
            return false;
        }

        $methodName = $this->actionName . 'Action';

        return method_exists($this->component, $methodName);
    }

    /**
     * Resolve the label value (handle closures).
     */
    private function resolveLabel(): string
    {
        if ($this->label instanceof Closure) {
            return call_user_func($this->label);
        }

        return $this->label ?? $this->id;
    }

    /**
     * Resolve the visible value (handle closures).
     */
    private function resolveVisible(): bool
    {
        if ($this->visible instanceof Closure) {
            return call_user_func($this->visible);
        }

        return $this->visible;
    }

    /**
     * Send error notification to the user.
     */
    private function notifyError(string $title, string $message): void
    {
        Notification::make()
            ->title($title)
            ->body($message)
            ->danger()
            ->send();
    }
}