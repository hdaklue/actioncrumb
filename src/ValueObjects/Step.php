<?php

namespace Hdaklue\Actioncrumb\ValueObjects;

class Step
{
    protected string $label;
    protected ?string $icon = null;
    protected ?string $url = null;
    protected ?string $route = null;
    protected array $routeParams = [];
    protected array $actions = [];
    protected bool $current = false;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public static function make(string $label): self
    {
        return new static($label);
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;
        $this->route = null;
        $this->routeParams = [];
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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getUrl(): ?string
    {
        return $this->url;
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
        return !$this->current && ($this->hasUrl() || $this->hasRoute());
    }

    public function getResolvedUrl(): ?string
    {
        if ($this->hasRoute()) {
            return route($this->route, $this->routeParams);
        }

        return $this->url;
    }
}