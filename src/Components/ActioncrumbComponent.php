<?php

namespace Hdaklue\Actioncrumb\Components;

use Hdaklue\Actioncrumb\Config\ActioncrumbConfig;
use Hdaklue\Actioncrumb\Renderers\ActioncrumbRenderer;
use Illuminate\View\Component;

class ActioncrumbComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $steps = [],
        public ?object $component = null
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $config = app(ActioncrumbConfig::class)->compact()->compactMenuOnMobile();

        return view('actioncrumb::components.actioncrumb', [
            'steps' => $this->steps,
            'config' => $config,
            'component' => $this->component,
        ]);
    }
}