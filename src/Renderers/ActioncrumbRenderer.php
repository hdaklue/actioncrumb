<?php

namespace Hdaklue\Actioncrumb\Renderers;

use Hdaklue\Actioncrumb\Config\ActioncrumbConfig;
use Illuminate\Support\Facades\View;

class ActioncrumbRenderer
{
    public function __construct(
        protected ActioncrumbConfig $config
    ) {}

    public function render(array $steps, $component = null): string
    {
        // Process steps to handle StepComponent instances
        $processedSteps = $this->processStepsForRendering($steps);

        return View::make('actioncrumb::components.actioncrumb', [
            'steps' => $processedSteps,
            'config' => $this->config,
            'component' => $component
        ])->render();
    }

    /**
     * Process steps to handle StepComponent instances for rendering
     */
    protected function processStepsForRendering(array $steps): array
    {
        $processedSteps = [];

        foreach ($steps as $step) {
            if ($step instanceof \Hdaklue\Actioncrumb\Components\StepComponent) {
                // For StepComponent, we'll render the component itself
                $processedSteps[] = $step;
            } elseif ($step instanceof \Hdaklue\Actioncrumb\Step) {
                // Regular Step remains the same
                $processedSteps[] = $step;
            }
        }

        return $processedSteps;
    }

    public static function make(ActioncrumbConfig $config): self
    {
        return new self($config);
    }
}