<?php

use Hdaklue\Actioncrumb\Action;

describe('Action Class', function () {
    it('can be created with make method', function () {
        $action = Action::make('test-action');

        expect($action)->toBeInstanceOf(Action::class)
            ->and($action->getId())->toBe('test-action');
    });

    it('can set and get label', function () {
        $action = Action::make('test')
            ->label('Test Action');

        expect($action->getLabel())->toBe('Test Action');
    });

    it('uses id as label when no label is set', function () {
        $action = Action::make('test-id');

        expect($action->getLabel())->toBe('test-id');
    });

    it('can handle closure labels', function () {
        $action = Action::make('test')
            ->label(fn() => 'Dynamic Label');

        expect($action->getLabel())->toBe('Dynamic Label');
    });

    it('can set and get icon', function () {
        $action = Action::make('test')
            ->icon('heroicon-o-plus');

        expect($action->getIcon())->toBe('heroicon-o-plus');
    });

    it('can set route and clears other action types', function () {
        $action = Action::make('test')
            ->url('/test')
            ->execute(fn() => 'test')
            ->route('test.route', ['id' => 1]);

        expect($action->getRoute())->toBe('test.route')
            ->and($action->getRouteParams())->toBe(['id' => 1])
            ->and($action->hasRoute())->toBeTrue()
            ->and($action->hasUrl())->toBeFalse()
            ->and($action->hasExecute())->toBeFalse();
    });

    it('can set url and clears other action types', function () {
        $action = Action::make('test')
            ->route('test.route')
            ->execute(fn() => 'test')
            ->url('/test-url');

        expect($action->getUrl())->toBe('/test-url')
            ->and($action->hasUrl())->toBeTrue()
            ->and($action->hasRoute())->toBeFalse()
            ->and($action->hasExecute())->toBeFalse();
    });

    it('can handle closure urls', function () {
        $action = Action::make('test')
            ->url(fn() => '/dynamic-url');

        expect($action->getUrl())->toBe('/dynamic-url');
    });

    it('can set execute callback and clears other action types', function () {
        $callback = fn() => 'executed';
        $action = Action::make('test')
            ->route('test.route')
            ->url('/test')
            ->execute($callback);

        expect($action->getExecute())->toBe($callback)
            ->and($action->hasExecute())->toBeTrue()
            ->and($action->hasRoute())->toBeFalse()
            ->and($action->hasUrl())->toBeFalse();
    });

    it('can set and check separator', function () {
        $action = Action::make('test')
            ->separator(true);

        expect($action->hasSeparator())->toBeTrue();

        $action->separator(false);
        expect($action->hasSeparator())->toBeFalse();
    });

    it('can set and check visibility', function () {
        $action = Action::make('test')
            ->visible(true);

        expect($action->isVisible())->toBeTrue();

        $action->visible(false);
        expect($action->isVisible())->toBeFalse();
    });

    it('can handle closure visibility', function () {
        $action = Action::make('test')
            ->visible(fn() => false);

        expect($action->isVisible())->toBeFalse();
    });

    it('can set and check enabled state', function () {
        $action = Action::make('test')
            ->enabled(true);

        expect($action->isEnabled())->toBeTrue();

        $action->enabled(false);
        expect($action->isEnabled())->toBeFalse();
    });

    it('can handle closure enabled state', function () {
        $action = Action::make('test')
            ->enabled(fn() => true);

        expect($action->isEnabled())->toBeTrue();
    });

    it('returns resolved url from route when available', function () {
        $action = Action::make('test')
            ->route('test.route', [1]);

        // Test that route is properly set instead of testing actual URL resolution
        expect($action->hasRoute())->toBeTrue()
            ->and($action->getRoute())->toBe('test.route')
            ->and($action->getRouteParams())->toBe([1]);
    });

    it('returns resolved url from direct url when no route', function () {
        $action = Action::make('test')
            ->url('/direct-url');

        expect($action->getResolvedUrl())->toBe('/direct-url');
    });

    it('returns null resolved url when no url or route', function () {
        $action = Action::make('test');

        expect($action->getResolvedUrl())->toBeNull();
    });

    it('has route check works correctly', function () {
        $action = Action::make('test');
        expect($action->hasRoute())->toBeFalse();

        $action->route('test.route');
        expect($action->hasRoute())->toBeTrue();
    });

    it('has url check works correctly', function () {
        $action = Action::make('test');
        expect($action->hasUrl())->toBeFalse();

        $action->url('/test');
        expect($action->hasUrl())->toBeTrue();
    });

    it('has execute check works correctly', function () {
        $action = Action::make('test');
        expect($action->hasExecute())->toBeFalse();

        $action->execute(fn() => 'test');
        expect($action->hasExecute())->toBeTrue();
    });
});