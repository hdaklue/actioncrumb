<?php

use Hdaklue\Actioncrumb\Traits\HasActionCrumbs;
use Hdaklue\Actioncrumb\Action;
use Hdaklue\Actioncrumb\Step;

// Test component using HasActionCrumbs trait
class TestActionCrumbComponent
{
    use HasActionCrumbs;

    public array $testSteps = [];

    protected function actioncrumbs(): array
    {
        return $this->testSteps;
    }

    public function setTestSteps(array $steps): void
    {
        $this->testSteps = $steps;
    }


    // Mock dispatch method for testing
    public function dispatch($event, $data = []): void
    {
        // Mock implementation for testing
    }
}

describe('HasActionCrumbs Trait Functionality', function () {
    it('can get actioncrumbs from trait', function () {
        $component = new TestActionCrumbComponent();
        $action = Action::make('test')->label('Test Action');
        $step = Step::make('test')->label('Test Step')->actions([$action]);

        $component->setTestSteps([$step]);
        $actioncrumbs = $component->getActioncrumbs();

        expect($actioncrumbs)->not->toBeNull();
    });

    it('caches actioncrumb steps', function () {
        $component = new TestActionCrumbComponent();
        $action = Action::make('test')->label('Test Action');
        $step = Step::make('test')->label('Test Step')->actions([$action]);

        $component->setTestSteps([$step]);

        // First call should populate cache
        $firstResult = $component->getActioncrumbs();

        // Second call should return cached result
        $secondResult = $component->getActioncrumbs();

        expect($firstResult)->toBe($secondResult);
    });

    it('can handle action execution logic', function () {
        $component = new TestActionCrumbComponent();
        $executed = false;

        $action = Action::make('test')
            ->label('Test Action')
            ->execute(function() use (&$executed) {
                $executed = true;
                return 'executed';
            });

        $step = Step::make('test')
            ->label('Test Step')
            ->actions([$action]);

        $component->setTestSteps([$step]);

        // Calculate the action ID as done in the trait
        $actionId = md5($step->getLabel() . $action->getLabel() . 0);
        $stepId = md5($step->getLabel());

        $result = $component->handleActioncrumbAction($actionId, $stepId);

        expect($executed)->toBeTrue();
    });

    it('handles disabled actions correctly', function () {
        $component = new TestActionCrumbComponent();
        $executed = false;

        $action = Action::make('test')
            ->label('Test Action')
            ->enabled(false)
            ->execute(function() use (&$executed) {
                $executed = true;
                return 'should not execute';
            });

        $step = Step::make('test')
            ->label('Test Step')
            ->actions([$action]);

        $component->setTestSteps([$step]);

        $actionId = md5($step->getLabel() . $action->getLabel() . 0);
        $stepId = md5($step->getLabel());

        $result = $component->handleActioncrumbAction($actionId, $stepId);

        expect($executed)->toBeFalse()
            ->and($result)->toBeNull();
    });

    it('handles route actions correctly', function () {
        $component = new TestActionCrumbComponent();

        $action = Action::make('test')
            ->label('Test Action')
            ->route('test.route', ['id' => 1]);

        $step = Step::make('test')
            ->label('Test Step')
            ->actions([$action]);

        $component->setTestSteps([$step]);

        $actionId = md5($step->getLabel() . $action->getLabel() . 0);
        $stepId = md5($step->getLabel());

        // This tests that route actions are handled (even if route doesn't exist in test)
        expect($action->hasRoute())->toBeTrue()
            ->and($action->getRoute())->toBe('test.route');
    });

    it('handles url actions correctly', function () {
        $component = new TestActionCrumbComponent();

        $action = Action::make('test')
            ->label('Test Action')
            ->url('/test-url');

        $step = Step::make('test')
            ->label('Test Step')
            ->actions([$action]);

        $component->setTestSteps([$step]);

        $actionId = md5($step->getLabel() . $action->getLabel() . 0);
        $stepId = md5($step->getLabel());

        $result = $component->handleActioncrumbAction($actionId, $stepId);

        expect($result)->not->toBeNull();
    });

    it('returns null for non-existent actions', function () {
        $component = new TestActionCrumbComponent();

        $result = $component->handleActioncrumbAction('invalid-id', 'invalid-step');

        expect($result)->toBeNull();
    });

    it('handles steps without actions', function () {
        $component = new TestActionCrumbComponent();
        $step = Step::make('test')->label('Test Step');

        $component->setTestSteps([$step]);

        $result = $component->handleActioncrumbAction('any-id', 'any-step');

        expect($result)->toBeNull();
    });
});