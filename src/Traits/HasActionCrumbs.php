<?php

namespace Hdaklue\Actioncrumb\Traits;

use Hdaklue\Actioncrumb\Action;
use Hdaklue\Actioncrumb\Configuration\ActioncrumbConfig;
use Hdaklue\Actioncrumb\Renderers\ActioncrumbRenderer;
use Hdaklue\Actioncrumb\Step;

trait HasActionCrumbs
{
    protected array $actioncrumbSteps = [];

    protected function actioncrumbs(): array
    {
        return [];
    }

    public function getActioncrumbs(): array
    {
        if (empty($this->actioncrumbSteps)) {
            $this->actioncrumbSteps = $this->actioncrumbs();
        }

        return $this->actioncrumbSteps;
    }

    /**
     * Clear cached actioncrumb steps to force refresh on next render
     */
    public function refreshActioncrumbs(): void
    {
        $this->actioncrumbSteps = [];
    }

    public function renderActioncrumbs(): string
    {
        $steps = $this->getActioncrumbs();
        $config = app(ActioncrumbConfig::class)->compact()->compactMenuOnMobile();

        return ActioncrumbRenderer::make($config)->render($steps, $this);
    }

    public function handleActioncrumbAction(string $actionId, string $stepId): mixed
    {
        $steps = $this->getActioncrumbs();

        foreach ($steps as $step) {
            if (!$step->hasActions()) {
                continue;
            }

            foreach ($step->getActions() as $index => $action) {
                $currentActionId = md5($step->getLabel() . $action->getLabel() . $index);

                if ($currentActionId === $actionId) {
                    // Check if action is enabled before executing
                    if (!$action->isEnabled()) {
                        return null;
                    }

                    if ($action->hasExecute()) {
                        $result = call_user_func($action->getExecute());

                        $this->dispatch('actioncrumb:action-executed', [
                            'action' => $action->getLabel(),
                            'step' => $step->getLabel(),
                            'result' => $result,
                        ]);

                        return $result;
                    }

                    if ($action->hasRoute()) {
                        return redirect()->route($action->getRoute(), $action->getRouteParams());
                    }

                    if ($action->hasUrl()) {
                        return redirect($action->getUrl());
                    }
                }
            }
        }

        return null;
    }

    protected function getListeners(): array
    {
        return array_merge(parent::getListeners() ?? [], [
            'actioncrumb:execute' => 'handleActioncrumbAction',
        ]);
    }
}
