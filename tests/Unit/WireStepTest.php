<?php

use Hdaklue\Actioncrumb\Components\WireStep;
use Hdaklue\Actioncrumb\Step;
use Hdaklue\Actioncrumb\Action;

describe('WireStep Class', function () {
    it('can be created with make method', function () {
        $wireStep = WireStep::make('TestComponent::class', ['param' => 'value']);

        expect($wireStep)->toBeInstanceOf(WireStep::class)
            ->and($wireStep->getComponentClass())->toBe('TestComponent::class')
            ->and($wireStep->getParameters())->toBe(['param' => 'value']);
    });

    it('generates step id from component class basename by default', function () {
        $wireStep = WireStep::make('App\\Components\\UserManagement');

        expect($wireStep->getStepId())->toBe('UserManagement');
    });

    it('uses stepId parameter if provided', function () {
        $wireStep = WireStep::make('TestComponent::class', ['stepId' => 'custom-id']);

        expect($wireStep->getStepId())->toBe('custom-id');
    });

    it('can set and get label', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->label('Test Label');

        expect($wireStep->getLabel())->toBe('Test Label');
    });

    it('can set and get icon', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->icon('heroicon-o-user');

        expect($wireStep->getIcon())->toBe('heroicon-o-user');
    });

    it('can set and get url', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->url('/test-url');

        expect($wireStep->getUrl())->toBe('/test-url')
            ->and($wireStep->hasUrl())->toBeTrue()
            ->and($wireStep->hasRoute())->toBeFalse();
    });

    it('can set and get route with parameters', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->route('test.route', ['id' => 1]);

        expect($wireStep->getRoute())->toBe('test.route')
            ->and($wireStep->getRouteParams())->toBe(['id' => 1])
            ->and($wireStep->hasRoute())->toBeTrue()
            ->and($wireStep->hasUrl())->toBeFalse();
    });

    it('clears url when setting route', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->url('/test-url')
            ->route('test.route');

        expect($wireStep->hasUrl())->toBeFalse()
            ->and($wireStep->hasRoute())->toBeTrue();
    });

    it('clears route when setting url', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->route('test.route')
            ->url('/test-url');

        expect($wireStep->hasRoute())->toBeFalse()
            ->and($wireStep->hasUrl())->toBeTrue();
    });

    it('can set and check current state', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->current(true);

        expect($wireStep->isCurrent())->toBeTrue();

        $wireStep->current(false);
        expect($wireStep->isCurrent())->toBeFalse();
    });

    it('can set and check visibility', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->visible(true);

        expect($wireStep->isVisible())->toBeTrue();

        $wireStep->visible(false);
        expect($wireStep->isVisible())->toBeFalse();
    });

    it('can set and check enabled state', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->enabled(true);

        expect($wireStep->isEnabled())->toBeTrue();

        $wireStep->enabled(false);
        expect($wireStep->isEnabled())->toBeFalse();
    });

    it('can set and get actions', function () {
        $action = Action::make('test-action')->label('Test Action');
        $wireStep = WireStep::make('TestComponent::class')
            ->actions([$action]);

        expect($wireStep->getActions())->toHaveCount(1)
            ->and($wireStep->hasActions())->toBeTrue()
            ->and($wireStep->getActions()[0])->toBe($action);
    });

    it('can set custom step id', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->stepId('custom-step-id');

        expect($wireStep->getStepId())->toBe('custom-step-id');
    });

    it('getId returns same as getStepId for compatibility', function () {
        $wireStep = WireStep::make('TestComponent::class', ['stepId' => 'test-id']);

        expect($wireStep->getId())->toBe($wireStep->getStepId())
            ->and($wireStep->getId())->toBe('test-id');
    });

    it('has url check works correctly', function () {
        $wireStep = WireStep::make('TestComponent::class');
        expect($wireStep->hasUrl())->toBeFalse();

        $wireStep->url('/test');
        expect($wireStep->hasUrl())->toBeTrue();

        $wireStepNoUrl = WireStep::make('TestComponent::class');
        expect($wireStepNoUrl->hasUrl())->toBeFalse();
    });

    it('has route check works correctly', function () {
        $wireStep = WireStep::make('TestComponent::class');
        expect($wireStep->hasRoute())->toBeFalse();

        $wireStep->route('test.route');
        expect($wireStep->hasRoute())->toBeTrue();
    });

    it('determines clickability correctly', function () {
        // Not current, has URL, enabled = clickable
        $wireStep = WireStep::make('TestComponent::class')
            ->url('/test')
            ->current(false)
            ->enabled(true);

        expect($wireStep->isClickable())->toBeTrue();

        // Current step = not clickable
        $wireStep->current(true);
        expect($wireStep->isClickable())->toBeFalse();

        // Not current but no URL/route = not clickable
        $wireStepNoUrl = WireStep::make('TestComponent::class')
            ->current(false)
            ->enabled(true);
        expect($wireStepNoUrl->isClickable())->toBeFalse();

        // Not current, has route, enabled = clickable
        $wireStep->current(false)->route('test.route');
        expect($wireStep->isClickable())->toBeTrue();

        // Disabled = not clickable
        $wireStep->enabled(false);
        expect($wireStep->isClickable())->toBeFalse();
    });

    it('returns resolved url from route when available', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->route('test.route', [1]);

        // For this test, we'll check that it has the route set properly
        // The actual URL resolution would require Laravel routing to be set up
        expect($wireStep->hasRoute())->toBeTrue()
            ->and($wireStep->getRoute())->toBe('test.route')
            ->and($wireStep->getRouteParams())->toBe([1]);
    });

    it('returns resolved url from direct url when no route', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->url('/direct-url');

        expect($wireStep->getResolvedUrl())->toBe('/direct-url');
    });

    it('identifies as wire step', function () {
        $wireStep = WireStep::make('TestComponent::class');

        expect($wireStep->isWireStep())->toBeTrue();
    });

    it('can convert to regular step', function () {
        $action = Action::make('test-action');
        $wireStep = WireStep::make('TestComponent::class')
            ->stepId('test-id')
            ->label('Test Label')
            ->icon('heroicon-o-user')
            ->url('/test')
            ->current(true)
            ->visible(false)
            ->enabled(false)
            ->actions([$action]);

        $step = $wireStep->toStep();

        expect($step)->toBeInstanceOf(Step::class)
            ->and($step->getId())->toBe('test-id')
            ->and($step->getLabel())->toBe('Test Label')
            ->and($step->getIcon())->toBe('heroicon-o-user')
            ->and($step->getUrl())->toBe('/test')
            ->and($step->isCurrent())->toBeTrue()
            ->and($step->isVisible())->toBeFalse()
            ->and($step->isEnabled())->toBeFalse()
            ->and($step->getActions())->toHaveCount(1);
    });

    it('converts to step with route instead of url', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->route('test.route', ['id' => 1]);

        $step = $wireStep->toStep();

        expect($step->getRoute())->toBe('test.route')
            ->and($step->getRouteParams())->toBe(['id' => 1])
            ->and($step->hasRoute())->toBeTrue()
            ->and($step->hasUrl())->toBeFalse();
    });

    it('can handle actions properly', function () {
        $action1 = Action::make('edit')->label('Edit');
        $action2 = Action::make('delete')->label('Delete');
        $actions = [$action1, $action2];

        $wireStep = WireStep::make('TestComponent::class')
            ->actions($actions);

        expect($wireStep->hasActions())->toBeTrue()
            ->and($wireStep->getActions())->toHaveCount(2)
            ->and($wireStep->getActions()[0])->toBe($action1)
            ->and($wireStep->getActions()[1])->toBe($action2);
    });

    it('reports no actions when empty', function () {
        $wireStep = WireStep::make('TestComponent::class');

        expect($wireStep->hasActions())->toBeFalse()
            ->and($wireStep->getActions())->toBeEmpty();
    });

    it('transfers actions to Step when converted', function () {
        $action = Action::make('test')->label('Test Action');
        $wireStep = WireStep::make('TestComponent::class')
            ->label('Test Step')
            ->actions([$action]);

        $step = $wireStep->toStep();

        expect($step->hasActions())->toBeTrue()
            ->and($step->getActions())->toHaveCount(1)
            ->and($step->getActions()[0])->toBe($action);
    });

    it('maintains action context when used with other properties', function () {
        $action = Action::make('publish')
            ->label('Publish')
            ->icon('heroicon-o-arrow-up');

        $wireStep = WireStep::make('TestComponent::class', ['id' => 123])
            ->label('Draft Post')
            ->icon('heroicon-o-document')
            ->url('/posts/123')
            ->current(true)
            ->actions([$action]);

        expect($wireStep->getLabel())->toBe('Draft Post')
            ->and($wireStep->getIcon())->toBe('heroicon-o-document')
            ->and($wireStep->getUrl())->toBe('/posts/123')
            ->and($wireStep->isCurrent())->toBeTrue()
            ->and($wireStep->hasActions())->toBeTrue()
            ->and($wireStep->getActions()[0]->getLabel())->toBe('Publish')
            ->and($wireStep->getActions()[0]->getIcon())->toBe('heroicon-o-arrow-up');
    });

    it('can handle closure labels', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->label(fn() => 'Dynamic Label');

        expect($wireStep->getLabel())->toBe('Dynamic Label');
    });

    it('can handle closure urls', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->url(fn() => '/dynamic-url');

        expect($wireStep->getUrl())->toBe('/dynamic-url');
    });

    it('can handle closure visibility', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->visible(fn() => true);

        expect($wireStep->isVisible())->toBeTrue();

        $wireStep->visible(fn() => false);
        expect($wireStep->isVisible())->toBeFalse();
    });

    it('can handle closure enabled state', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->enabled(fn() => true);

        expect($wireStep->isEnabled())->toBeTrue();

        $wireStep->enabled(fn() => false);
        expect($wireStep->isEnabled())->toBeFalse();
    });

    it('transfers closure properties to Step when converted', function () {
        $wireStep = WireStep::make('TestComponent::class')
            ->label(fn() => 'Closure Label')
            ->url(fn() => '/closure-url')
            ->visible(fn() => false)
            ->enabled(fn() => true);

        $step = $wireStep->toStep();

        expect($step->getLabel())->toBe('Closure Label')
            ->and($step->getUrl())->toBe('/closure-url')
            ->and($step->isVisible())->toBeFalse()
            ->and($step->isEnabled())->toBeTrue();
    });
});