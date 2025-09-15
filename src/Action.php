<?php

namespace Hdaklue\Actioncrumb;

class Action
{
    protected string $id;
    protected string|\Closure|null $label = null;
    protected ?string $icon = null;
    protected ?string $route = null;
    protected array $routeParams = [];
    protected string|\Closure|null $url = null;
    protected ?\Closure $execute = null;
    protected bool $separator = false;
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

    public function route(string $route, array $params = []): self
    {
        $this->route = $route;
        $this->routeParams = $params;
        $this->url = null;
        $this->execute = null;
        return $this;
    }

    public function url(string|\Closure $url): self
    {
        $this->url = $url;
        $this->route = null;
        $this->routeParams = [];
        $this->execute = null;
        return $this;
    }

    public function label(string|\Closure $label): self
    {
        $this->label = $label;
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
        return is_callable($this->url)
            ? call_user_func($this->url)
            : $this->url;
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

        return $this->getUrl();
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
}