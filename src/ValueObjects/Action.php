<?php

namespace Hdaklue\Actioncrumb\ValueObjects;

class Action
{
    protected string $label;
    protected ?string $icon = null;
    protected ?string $route = null;
    protected array $routeParams = [];
    protected ?string $url = null;
    protected ?\Closure $execute = null;
    protected bool $separator = false;

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

    public function route(string $route, array $params = []): self
    {
        $this->route = $route;
        $this->routeParams = $params;
        $this->url = null;
        $this->execute = null;
        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;
        $this->route = null;
        $this->routeParams = [];
        $this->execute = null;
        return $this;
    }

    public function execute(\Closure $callback): self
    {
        $this->execute = $callback;
        $this->route = null;
        $this->routeParams = [];
        $this->url = null;
        return $this;
    }

    public function separator(bool $separator = true): self
    {
        $this->separator = $separator;
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

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getExecute(): ?\Closure
    {
        return $this->execute;
    }

    public function hasSeparator(): bool
    {
        return $this->separator;
    }

    public function hasRoute(): bool
    {
        return !is_null($this->route);
    }

    public function hasUrl(): bool
    {
        return !is_null($this->url);
    }

    public function hasExecute(): bool
    {
        return !is_null($this->execute);
    }

    public function getResolvedUrl(): ?string
    {
        if ($this->hasRoute()) {
            return route($this->route, $this->routeParams);
        }

        return $this->url;
    }
}