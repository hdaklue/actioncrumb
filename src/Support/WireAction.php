<?php

declare(strict_types=1);

namespace Hdaklue\Actioncrumb\Support;

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
 * WireAction::make('Create Document')
 *     ->livewire($this)
 *     ->icon('heroicon-o-plus')
 *     ->execute('createDocument');
 * ```
 */
final class WireAction
{
    private string $label;

    private ?string $icon = null;

    private ?HasActions $component = null;

    private ?string $actionName = null;

    private array $parameters = [];

    private bool $validated = true;

    private function __construct(string $label)
    {
        $this->label = $label;
    }

    /**
     * Create a new WireAction.
     */
    public static function make(string $label): self
    {
        return new self($label);
    }

    /**
     * Create multiple WireActions at once.
     * 
     * @param HasActions $component The Livewire component with actions
     * @param array $actions Array of action configurations:
     *                      [
     *                          'label' => 'Action Label',
     *                          'action' => 'actionMethodName',
     *                          'icon' => 'heroicon-o-star',
     *                          'parameters' => [],
     *                          'validated' => true
     *                      ]
     * @return Action[] Array of hdaklue/actioncrumb Action instances
     */
    public static function bulk(HasActions $component, array $actions): array
    {
        $result = [];

        foreach ($actions as $config) {
            $label = $config['label'];
            $actionName = $config['action'];
            $icon = $config['icon'] ?? null;
            $parameters = $config['parameters'] ?? [];
            $validated = $config['validated'] ?? true;

            $wireAction = self::make($label)
                ->livewire($component)
                ->validated($validated);

            if ($icon) {
                $wireAction->icon($icon);
            }

            if (! empty($parameters)) {
                $wireAction->parameters($parameters);
            }

            $result[] = $wireAction->execute($actionName);
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
     * Set the action icon (Heroicon identifier).
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;

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
     * @return Action The actioncrumb Action instance
     */
    public function execute(string $actionName, array $parameters = []): Action
    {
        $this->actionName = $actionName;
        $this->parameters = array_merge($this->parameters, $parameters);

        return Action::make($this->label)
            ->icon($this->icon)
            ->execute(fn () => $this->executeAction());
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