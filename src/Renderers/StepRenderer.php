<?php

declare(strict_types=1);

namespace Hdaklue\Actioncrumb\Renderers;

use Hdaklue\Actioncrumb\Config\ActioncrumbConfig;
use Hdaklue\Actioncrumb\Components\WireStep;
use Hdaklue\Actioncrumb\Step;
use Illuminate\Support\Facades\View;

/**
 * StepRenderer - Handles rendering of mixed Step and WireStep objects
 */
class StepRenderer
{
    public function __construct(
        protected ActioncrumbConfig $config
    ) {}

    /**
     * Render a collection of steps (mix of Step and WireStep objects)
     */
    public function render(array $steps, $component = null): string
    {
        $processedSteps = $this->processStepsForRendering($steps);

        return View::make('hdaklue.actioncrumb::components.actioncrumb', [
            'steps' => $processedSteps,
            'config' => $this->config,
            'component' => $component
        ])->render();
    }

    /**
     * Process steps to handle both Step and WireStep instances
     */
    protected function processStepsForRendering(array $steps): array
    {
        $processedSteps = [];

        foreach ($steps as $step) {
            if ($step instanceof WireStep) {
                // For WireStep, we need special handling
                $processedSteps[] = $this->processWireStep($step);
            } elseif ($step instanceof Step) {
                // Regular Step remains the same
                $processedSteps[] = $step;
            } else {
                // Log warning for invalid step types
                if (function_exists('logger')) {
                    logger()->warning('Invalid step type in crumbSteps', [
                        'type' => is_object($step) ? get_class($step) : gettype($step),
                        'step' => $step
                    ]);
                }
            }
        }

        return $processedSteps;
    }

    /**
     * Process a WireStep for rendering
     */
    protected function processWireStep(WireStep $wireStep): mixed
    {
        try {
            // Check if the WireStep's component class exists
            if (!class_exists($wireStep->getComponentClass())) {
                if (function_exists('logger')) {
                    logger()->warning('WireStep component class not found', [
                        'class' => $wireStep->getComponentClass(),
                        'stepId' => $wireStep->getStepId()
                    ]);
                }
                // Fallback to regular Step
                return $wireStep->toStep();
            }

            // Return the WireStep to be handled by the template
            return $wireStep;
        } catch (\Exception $e) {
            // If something goes wrong, fallback to a regular Step
            if (function_exists('logger')) {
                logger()->error('Error processing WireStep', [
                    'error' => $e->getMessage(),
                    'class' => $wireStep->getComponentClass(),
                    'stepId' => $wireStep->getStepId()
                ]);
            }

            return $wireStep->toStep();
        }
    }

    /**
     * Check if any steps are WireSteps
     */
    public function hasWireSteps(array $steps): bool
    {
        foreach ($steps as $step) {
            if ($step instanceof WireStep) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get only regular Steps from a mixed array
     */
    public function getRegularSteps(array $steps): array
    {
        return array_filter($steps, fn($step) => $step instanceof Step);
    }

    /**
     * Get only WireSteps from a mixed array
     */
    public function getWireSteps(array $steps): array
    {
        return array_filter($steps, fn($step) => $step instanceof WireStep);
    }

    /**
     * Static factory method
     */
    public static function make(ActioncrumbConfig $config): self
    {
        return new self($config);
    }
}