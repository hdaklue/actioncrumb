<?php

namespace Hdaklue\Actioncrumb\Renderers;

use Hdaklue\Actioncrumb\Configuration\ActioncrumbConfig;
use Illuminate\Support\Facades\View;

class ActioncrumbRenderer
{
    public function __construct(
        protected ActioncrumbConfig $config
    ) {}

    public function render(array $steps, $component = null): string
    {
        return View::make('actioncrumb::components.actioncrumb', [
            'steps' => $steps,
            'config' => $this->config,
            'component' => $component
        ])->render();
    }

    public static function make(ActioncrumbConfig $config): self
    {
        return new self($config);
    }
}