<?php

declare(strict_types=1);

namespace Hdaklue\Actioncrumb\Traits;

use Hdaklue\Actioncrumb\Config\ActioncrumbConfig;
use Hdaklue\Actioncrumb\Renderers\StepRenderer;

trait HasCrumbSteps
{
    /**
     * Define the breadcrumb steps for this component
     */
    abstract protected function crumbSteps(): array;

    /**
     * Render the breadcrumb steps
     */
    public function renderCrumbSteps(): string
    {
        $config = app(ActioncrumbConfig::class);
        $renderer = StepRenderer::make($config);

        return $renderer->render($this->crumbSteps(), $this);
    }

    /**
     * Refresh the breadcrumb steps (useful for dynamic updates)
     */
    public function refreshCrumbSteps(): void
    {
        $this->dispatch('crumb-steps:refreshed');
    }

    /**
     * Get processed steps for rendering
     */
    protected function getProcessedSteps(): array
    {
        return $this->crumbSteps();
    }
}