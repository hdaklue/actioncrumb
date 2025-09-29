<?php

namespace Hdaklue\Actioncrumb\Traits;

use Hdaklue\Actioncrumb\Action;
use Hdaklue\Actioncrumb\Config\ActioncrumbConfig;
use Hdaklue\Actioncrumb\Renderers\ActioncrumbRenderer;
use Hdaklue\Actioncrumb\Step;

/**
 * HasActionCrumbs trait - For individual step components that provide actions
 *
 * This trait is used by components that only need to provide actions (not navigation structure).
 * The actioncrumbs() method MUST return an array of Action objects only.
 *
 * For navigation breadcrumb components, use HasCrumbSteps trait instead.
 *
 * Usage:
 * protected function actioncrumbs(): array
 * {
 *     return [
 *         Action::make('Edit')->execute(fn() => $this->edit()),
 *         Action::make('Delete')->execute(fn() => $this->delete()),
 *     ];
 * }
 */
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
     * Only handles Action objects - actioncrumbs() must return Action[] array
     */
    protected function getActionsFromCrumbs(): array
    {
        $actions = $this->getActioncrumbs();
        $validActions = [];

        foreach ($actions as $action) {
            if ($action instanceof Action) {
                $validActions[] = $action;
            }
        }

        return $validActions;
    }

    public function handleActioncrumbAction(string $actionId, string $stepId): mixed
    {
        $actions = $this->getActioncrumbs();
        $stepLabel = class_basename(static::class);

        foreach ($actions as $index => $action) {
            if (!($action instanceof Action)) {
                continue;
            }

            $currentActionId = md5($stepLabel . $action->getLabel() . $index);

            if ($currentActionId === $actionId) {
                // Check if the action is enabled before executing
                if (!$action->isEnabled()) {
                    return null;
                }

                if ($action->hasExecute()) {
                    $result = call_user_func($action->getExecute());

                    $this->dispatch('actioncrumb:action-executed', [
                        'action' => $action->getLabel(),
                        'step' => $stepLabel,
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

        return null;
    }

}
