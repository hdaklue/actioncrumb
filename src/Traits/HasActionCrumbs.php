<?php

namespace Hdaklue\Actioncrumb\Traits;

use Hdaklue\Actioncrumb\Action;
use Hdaklue\Actioncrumb\Config\ActioncrumbConfig;
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

    /**
     * Render only the actions/dropdown portion for WireStep components
     * This allows individual components to render just their actions
     */
    public function renderStepActions(?string $stepLabel = null, array $actions = null): string
    {
        $actions = $actions ?? $this->getActionsFromCrumbs();
        $stepLabel = $stepLabel ?? class_basename(static::class);

        if (empty($actions)) {
            return '';
        }

        $config = app(ActioncrumbConfig::class);

        return view('actioncrumb::components.step-actions', [
            'actions' => $actions,
            'stepLabel' => $stepLabel,
            'config' => $config,
            'component' => $this
        ])->render();
    }

    /**
     * Extract actions from actioncrumbs for the current component
     */
    protected function getActionsFromCrumbs(): array
    {
        $steps = $this->getActioncrumbs();
        $allActions = [];

        foreach ($steps as $step) {
            if ($step->hasActions()) {
                $allActions = array_merge($allActions, $step->getActions());
            }
        }

        return $allActions;
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
                    // Check if the action is enabled before executing
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

}
