<?php

declare(strict_types=1);

namespace Hdaklue\Actioncrumb\Components;

use Hdaklue\Actioncrumb\Step;
use Livewire\Mechanisms\ComponentRegistry;

/**
 * WireStep - Transporter for embedding Livewire components as breadcrumb steps
 *
 * Usage: WireStep::make(FlowStep::class, ['flowId' => $id, 'tenant' => $tenant])
 */
class WireStep
{
    protected string $componentClass;
    protected array $parameters;
    protected string $stepId;
    protected ?string $label = null;
    protected ?string $icon = null;
    protected ?string $url = null;
    protected ?string $route = null;
    protected array $routeParams = [];
    protected bool $current = false;
    protected bool $visible = true;
    protected bool $enabled = true;

    /**
     * Optional actions associated with this step (for menus/modals)
     * Matches the API surface of Hdaklue\Actioncrumb\Step where needed
     * @var array<int, \Hdaklue\Actioncrumb\Action>
     */
    protected array $actions = [];

    protected function __construct(string $componentClass, array $parameters = [])
    {
        $this->componentClass = $componentClass;
        $this->parameters = $parameters;
        $this->stepId = $parameters['stepId'] ?? class_basename($componentClass);
    }

    /**
     * Create a new WireStep transporter
     */
    public static function make(string $componentClass, array $parameters = []): self
    {
        return new self($componentClass, $parameters);
    }

    /**
     * Set step label
     */
    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Set step icon
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Set step URL
     */
    public function url(string $url): self
    {
        $this->url = $url;
        $this->route = null;
        $this->routeParams = [];
        return $this;
    }

    /**
     * Set step route
     */
    public function route(string $route, array $params = []): self
    {
        $this->route = $route;
        $this->routeParams = $params;
        $this->url = null;
        return $this;
    }

    /**
     * Mark as current step
     */
    public function current(bool $current = true): self
    {
        $this->current = $current;
        return $this;
    }

    /**
     * Set visibility
     */
    public function visible(bool $visible = true): self
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * Set enabled state
     */
    public function enabled(bool $enabled = true): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Set the actions associated with this step
     */
    public function actions(array $actions): self
    {
        $this->actions = $actions;
        return $this;
    }

    /**
     * Set custom step ID
     */
    public function stepId(string $stepId): self
    {
        $this->stepId = $stepId;
        return $this;
    }

    /**
     * Get the component class
     */
    public function getComponentClass(): string
    {
        return $this->componentClass;
    }

    /**
     * Get the parameters for the component
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Get step ID
     */
    public function getStepId(): string
    {
        return $this->stepId;
    }

    /**
     * Get step label
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Get step icon
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Get step URL
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Get step route
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * Get route parameters
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * Get actions for this step
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Get resolved URL (route or direct URL)
     */
    public function getResolvedUrl(): ?string
    {
        if ($this->route) {
            return route($this->route, $this->routeParams);
        }

        return $this->url;
    }

    /**
     * Check if current step
     */
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * Check if visible
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Check if enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Whether this step has actions
     */
    public function hasActions(): bool
    {
        return count($this->actions) > 0;
    }

    /**
     * Check if this is a WireStep (for template differentiation)
     */
    public function isWireStep(): bool
    {
        return true;
    }

    /**
     * Render the embedded Livewire component
     */
    public function render(): string
    {
        $componentRegistry = app(ComponentRegistry::class);
        $componentName = $componentRegistry->getName($this->componentClass);

        return app('livewire')->mount($componentName, $this->parameters)->html();
    }

    /**
     * Convert to a regular Step for fallback rendering
     */
    public function toStep(): Step
    {
        $step = Step::make($this->stepId);

        if ($this->label) {
            $step->label($this->label);
        }

        if ($this->icon) {
            $step->icon($this->icon);
        }

        if ($this->url) {
            $step->url($this->url);
        } elseif ($this->route) {
            $step->route($this->route, $this->routeParams);
        }

        $step->current($this->current);
        $step->visible($this->visible);
        $step->enabled($this->enabled);
        if (!empty($this->actions)) {
            $step->actions($this->actions);
        }

        return $step;
    }
}