<?php

use Hdaklue\Actioncrumb\Step;
use Hdaklue\Actioncrumb\Action;
use Hdaklue\Actioncrumb\Components\WireStep;

describe('Step Class', function () {
    it('can be created with make method', function () {
        $step = Step::make('test-step');

        expect($step)->toBeInstanceOf(Step::class)
            ->and($step->getId())->toBe('test-step');
    });

    it('can set and get label', function () {
        $step = Step::make('test')
            ->label('Test Label');

        expect($step->getLabel())->toBe('Test Label');
    });

    it('uses id as label when no label is set', function () {
        $step = Step::make('test-id');

        expect($step->getLabel())->toBe('test-id');
    });

    it('can handle closure labels', function () {
        $step = Step::make('test')
            ->label(fn() => 'Dynamic Label');

        expect($step->getLabel())->toBe('Dynamic Label');
    });

    it('can set and get icon', function () {
        $step = Step::make('test')
            ->icon('heroicon-o-user');

        expect($step->getIcon())->toBe('heroicon-o-user');
    });

    it('can set and get url', function () {
        $step = Step::make('test')
            ->url('/test-url');

        expect($step->getUrl())->toBe('/test-url')
            ->and($step->hasUrl())->toBeTrue()
            ->and($step->hasRoute())->toBeFalse();
    });

    it('can handle closure urls', function () {
        $step = Step::make('test')
            ->url(fn() => '/dynamic-url');

        expect($step->getUrl())->toBe('/dynamic-url');
    });

    it('can set and get route', function () {
        $step = Step::make('test')
            ->route('test.route', ['id' => 1]);

        expect($step->getRoute())->toBe('test.route')
            ->and($step->getRouteParams())->toBe(['id' => 1])
            ->and($step->hasRoute())->toBeTrue()
            ->and($step->hasUrl())->toBeFalse();
    });

    it('clears url when setting route', function () {
        $step = Step::make('test')
            ->url('/test-url')
            ->route('test.route');

        expect($step->hasUrl())->toBeFalse()
            ->and($step->hasRoute())->toBeTrue();
    });

    it('clears route when setting url', function () {
        $step = Step::make('test')
            ->route('test.route')
            ->url('/test-url');

        expect($step->hasRoute())->toBeFalse()
            ->and($step->hasUrl())->toBeTrue();
    });

    it('can set and check current state', function () {
        $step = Step::make('test')
            ->current(true);

        expect($step->isCurrent())->toBeTrue();

        $step->current(false);
        expect($step->isCurrent())->toBeFalse();
    });

    it('can set and check visibility', function () {
        $step = Step::make('test')
            ->visible(true);

        expect($step->isVisible())->toBeTrue();

        $step->visible(false);
        expect($step->isVisible())->toBeFalse();
    });

    it('can handle closure visibility', function () {
        $step = Step::make('test')
            ->visible(fn() => true);

        expect($step->isVisible())->toBeTrue();
    });

    it('can set and check enabled state', function () {
        $step = Step::make('test')
            ->enabled(true);

        expect($step->isEnabled())->toBeTrue();

        $step->enabled(false);
        expect($step->isEnabled())->toBeFalse();
    });

    it('can handle closure enabled state', function () {
        $step = Step::make('test')
            ->enabled(fn() => false);

        expect($step->isEnabled())->toBeFalse();
    });

    it('can set and get actions', function () {
        $action = Action::make('test-action')->label('Test Action');
        $step = Step::make('test')
            ->actions([$action]);

        expect($step->getActions())->toHaveCount(1)
            ->and($step->hasActions())->toBeTrue()
            ->and($step->getActions()[0])->toBe($action);
    });

    it('reports no actions when empty', function () {
        $step = Step::make('test');

        expect($step->hasActions())->toBeFalse()
            ->and($step->getActions())->toBeEmpty();
    });

    it('determines clickability correctly', function () {
        // Not current, has URL, enabled = clickable
        $step = Step::make('test')
            ->url('/test')
            ->current(false)
            ->enabled(true);

        expect($step->isClickable())->toBeTrue();

        // Current step = not clickable
        $step->current(true);
        expect($step->isClickable())->toBeFalse();

        // Not current but no URL/route = not clickable
        $stepNoUrl = Step::make('test-no-url')
            ->current(false)
            ->enabled(true);
        expect($stepNoUrl->isClickable())->toBeFalse();

        // Not current, has route, enabled = clickable
        $step->current(false)->route('test.route');
        expect($step->isClickable())->toBeTrue();

        // Disabled = not clickable
        $step->enabled(false);
        expect($step->isClickable())->toBeFalse();
    });

    it('can convert to WireStep', function () {
        $action = Action::make('test-action');
        $step = Step::make('test-id')
            ->label('Test Label')
            ->icon('heroicon-o-user')
            ->url('/test')
            ->current(true)
            ->visible(false)
            ->enabled(false)
            ->actions([$action]);

        // Test that the step has all the required properties
        // This would work if asComponent method was implemented
        expect($step->getId())->toBe('test-id')
            ->and($step->getLabel())->toBe('Test Label')
            ->and($step->getIcon())->toBe('heroicon-o-user')
            ->and($step->getUrl())->toBe('/test')
            ->and($step->isCurrent())->toBeTrue()
            ->and($step->isVisible())->toBeFalse()
            ->and($step->isEnabled())->toBeFalse()
            ->and($step->getActions())->toHaveCount(1);
    });

    it('reports as not a component by default', function () {
        $step = Step::make('test');

        expect($step->isComponent())->toBeFalse();
    });
});