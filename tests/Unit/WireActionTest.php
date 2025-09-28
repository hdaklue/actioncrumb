<?php

use Hdaklue\Actioncrumb\Support\WireAction;
use Hdaklue\Actioncrumb\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Mockery\MockInterface;

describe('WireAction Class', function () {
    beforeEach(function () {
        // Mock Notification to avoid actual notification sending in tests
        if (!class_exists('Filament\\Notifications\\Notification')) {
            eval('
                namespace Filament\\Notifications;
                class Notification {
                    public static function make() { return new self(); }
                    public function title($title) { return $this; }
                    public function body($body) { return $this; }
                    public function danger() { return $this; }
                    public function send() { return $this; }
                }
            ');
        }
    });

    it('can be created with make method', function () {
        $wireAction = WireAction::make('test-action');

        expect($wireAction)->toBeInstanceOf(WireAction::class);
    });

    it('can set livewire component', function () {
        $component = \Mockery::mock(HasActions::class);
        $wireAction = WireAction::make('test')
            ->livewire($component);

        // We can't directly test the private property, but we can test the behavior
        expect($wireAction)->toBeInstanceOf(WireAction::class);
    });

    it('can set label', function () {
        $wireAction = WireAction::make('test')
            ->label('Test Action');

        expect($wireAction)->toBeInstanceOf(WireAction::class);
    });

    it('can set label with closure', function () {
        $wireAction = WireAction::make('test')
            ->label(fn() => 'Dynamic Label');

        expect($wireAction)->toBeInstanceOf(WireAction::class);
    });

    it('can set icon', function () {
        $wireAction = WireAction::make('test')
            ->icon('heroicon-o-plus');

        expect($wireAction)->toBeInstanceOf(WireAction::class);
    });

    it('can set visibility', function () {
        $wireAction = WireAction::make('test')
            ->visible(true);

        expect($wireAction)->toBeInstanceOf(WireAction::class);
    });

    it('can set visibility with closure', function () {
        $wireAction = WireAction::make('test')
            ->visible(fn() => false);

        expect($wireAction)->toBeInstanceOf(WireAction::class);
    });

    it('can set parameters', function () {
        $wireAction = WireAction::make('test')
            ->parameters(['key' => 'value']);

        expect($wireAction)->toBeInstanceOf(WireAction::class);
    });

    it('can set validation flag', function () {
        $wireAction = WireAction::make('test')
            ->validated(false);

        expect($wireAction)->toBeInstanceOf(WireAction::class);
    });

    it('returns null when not visible', function () {
        $component = \Mockery::mock(HasActions::class);
        $wireAction = WireAction::make('test')
            ->livewire($component)
            ->visible(false);

        $result = $wireAction->execute('testAction');

        expect($result)->toBeNull();
    });

    it('returns action instance when visible', function () {
        $component = \Mockery::mock(HasActions::class);

        $wireAction = WireAction::make('test')
            ->livewire($component)
            ->label('Test Action')
            ->visible(true);

        $result = $wireAction->execute('testAction');

        expect($result)->toBeInstanceOf(Action::class);
    });

    it('can create bulk actions', function () {
        $component = \Mockery::mock(HasActions::class);

        $actionsConfig = [
            [
                'label' => 'First Action',
                'action' => 'firstAction',
                'icon' => 'heroicon-o-plus',
            ],
            [
                'id' => 'custom-id',
                'label' => 'Second Action',
                'action' => 'secondAction',
                'visible' => true,
            ]
        ];

        $actions = WireAction::bulk($component, $actionsConfig);

        expect($actions)->toHaveCount(2)
            ->and($actions[0])->toBeInstanceOf(Action::class)
            ->and($actions[1])->toBeInstanceOf(Action::class);
    });

    it('filters out invisible actions in bulk creation', function () {
        $component = \Mockery::mock(HasActions::class);

        $actionsConfig = [
            [
                'label' => 'Visible Action',
                'action' => 'visibleAction',
                'visible' => true,
            ],
            [
                'label' => 'Hidden Action',
                'action' => 'hiddenAction',
                'visible' => false,
            ]
        ];

        $actions = WireAction::bulk($component, $actionsConfig);

        expect($actions)->toHaveCount(1);
    });

    it('can debug component actions', function () {
        $component = \Mockery::mock(HasActions::class);
        $component->shouldReceive('__class')->andReturn('TestComponent');

        $debugInfo = WireAction::debug($component);

        expect($debugInfo)->toBeArray()
            ->and($debugInfo)->toHaveKey('class')
            ->and($debugInfo)->toHaveKey('available_actions');
    });

    afterEach(function () {
        \Mockery::close();
    });
});