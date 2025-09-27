<?php

declare(strict_types=1);

namespace Hdaklue\Actioncrumb\Components;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Hdaklue\Actioncrumb\Traits\HasActionCrumbs;
use Livewire\Component;

class WireStep extends Component implements HasActions, HasSchemas
{
    use HasActionCrumbs;
    use InteractsWithActions;
    use InteractsWithSchemas;

    public string $stepId;
    public string|\Closure|null $label = null;
    public ?string $icon = null;
    public string|\Closure|null $url = null;
    public ?string $route = null;
    public array $routeParams = [];
    public bool $current = false;
    public bool|\Closure $visible = true;
    public bool|\Closure $enabled = true;
    public ?HasActions $parent = null;
    public array $stepData = [];

    protected $listeners = [
        'actioncrumb:execute' => 'handleActioncrumbAction',
        'step:refresh' => 'refreshStep',
    ];

    public function mount(
        string $stepId,
        string|\Closure|null $label = null,
        ?string $icon = null,
        string|\Closure|null $url = null,
        ?string $route = null,
        array $routeParams = [],
        bool $current = false,
        bool|\Closure $visible = true,
        bool|\Closure $enabled = true,
        ?HasActions $parent = null,
        array $stepData = []
    ): void {
        $this->stepId = $stepId;
        $this->label = $label;
        $this->icon = $icon;
        $this->url = $url;
        $this->route = $route;
        $this->routeParams = $routeParams;
        $this->current = $current;
        $this->visible = $visible;
        $this->enabled = $enabled;
        $this->parent = $parent;
        $this->stepData = $stepData;
    }

    public static function make(string $stepId): self
    {
        $instance = new static();
        $instance->stepId = $stepId;
        return $instance;
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

    public function stepData(array $data): self
    {
        $this->stepData = $data;
        return $this;
    }

    public function parent(?HasActions $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    public function getId(): string
    {
        return $this->stepId;
    }

    public function getLabel(): string
    {
        if ($this->label === null) {
            return $this->stepId;
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

    public function getResolvedUrl(): ?string
    {
        if ($this->hasRoute()) {
            return route($this->route, $this->routeParams);
        }

        return $this->getUrl();
    }

    public function isCurrent(): bool
    {
        return $this->current;
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

    public function hasActions(): bool
    {
        return count($this->getActioncrumbs()) > 0;
    }

    public function getActions(): array
    {
        return $this->getActioncrumbs();
    }

    public function refreshStep(): void
    {
        $this->refreshActioncrumbs();
        $this->dispatch('step:refreshed', ['stepId' => $this->stepId]);
    }

    public function getStepData(string $key = null)
    {
        if ($key === null) {
            return $this->stepData;
        }

        return $this->stepData[$key] ?? null;
    }

    public function setStepData(string $key, $value): void
    {
        $this->stepData[$key] = $value;
    }

    protected function actioncrumbs(): array
    {
        return [];
    }

    public function render()
    {
        return view('hdaklue.actioncrumb::components.step', [
            'step' => $this,
            'config' => app(\Hdaklue\Actioncrumb\Config\ActioncrumbConfig::class)->compact()->compactMenuOnMobile()
        ]);
    }

    protected function getListeners(): array
    {
        return array_merge(parent::getListeners() ?? [], [
            'actioncrumb:execute' => 'handleActioncrumbAction',
            'step:refresh' => 'refreshStep',
        ]);
    }
}