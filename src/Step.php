<?php

namespace Hdaklue\Actioncrumb;

use Filament\Actions\Contracts\HasActions;

class Step
{
    protected string $id;
    protected string|\Closure|null $label = null;
    protected ?string $icon = null;
    protected string|\Closure|null $url = null;
    protected ?string $route = null;
    protected array $routeParams = [];
    protected array $actions = [];
    protected bool $current = false;
    protected bool|\Closure $visible = true;
    protected bool|\Closure $enabled = true;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function make(string $id): self
    {
        return new static($id);
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function url(string|\Closure $url): self
    {
        $this->url = $url;
        $this->route = null;
        $this->routeParams = [];
        return $this;
    }

    public function label(string|\Closure $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function route(string $route, array $params = []): self
    {
        $this->route = $route;
        $this->routeParams = $params;
        $this->url = null;
        return $this;
    }

    public function actions(array $actions): self
    {
        $this->actions = $actions;
        return $this;
    }

    public function current(bool $current = true): self
    {
        $this->current = $current;
        return $this;
    }

    public function visible(bool|\Closure $visible): self
    {
        $this->visible = $visible;
        return $this;
    }

    public function enabled(bool|\Closure $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        if ($this->label === null) {
            return $this->id;
        }
        
        return is_callable($this->label)
            ? call_user_func($this->label)
            : $this->label;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getUrl(): ?string
    {
        return is_callable($this->url)
            ? call_user_func($this->url)
            : $this->url;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function isCurrent(): bool
    {
        return $this->current;
    }

    public function hasActions(): bool
    {
        return count($this->actions) > 0;
    }

    public function hasRoute(): bool
    {
        return !is_null($this->route);
    }

    public function hasUrl(): bool
    {
        return !is_null($this->url);
    }

    public function isClickable(): bool
    {
        return !$this->current && ($this->hasUrl() || $this->hasRoute()) && $this->isEnabled();
    }

    public function isVisible(): bool
    {
        return is_callable($this->visible) 
            ? call_user_func($this->visible) 
            : $this->visible;
    }

    public function isEnabled(): bool
    {
        return is_callable($this->enabled) 
            ? call_user_func($this->enabled) 
            : $this->enabled;
    }

    public function getResolvedUrl(): ?string
    {
        if ($this->hasRoute()) {
            return route($this->route, $this->routeParams);
        }

        return $this->getUrl();
    }

    /**
     * Convert this Step to a WireStep for advanced functionality
     */
    public function asComponent(?HasActions $parent = null): \Hdaklue\Actioncrumb\Components\WireStep
    {
        $component = new \Hdaklue\Actioncrumb\Components\WireStep();
        $component->stepId = $this->id;
        $component->label = $this->label;
        $component->icon = $this->icon;
        $component->url = $this->url;
        $component->route = $this->route;
        $component->routeParams = $this->routeParams;
        $component->current = $this->current;
        $component->visible = $this->visible;
        $component->enabled = $this->enabled;
        $component->parent = $parent;

        return $component;
    }

    /**
     * Check if this step should be rendered as a component
     */
    public function isComponent(): bool
    {
        return false;
    }
}