<?php

use Hdaklue\Actioncrumb\Support\WireAction;
use Hdaklue\Actioncrumb\Action as CrumbAction;
use Hdaklue\Actioncrumb\Step;
use Filament\Actions\Contracts\HasActions;

describe('WireAction Integration', function () {
    it('can create wireaction instances', function () {
        $component = \Mockery::mock(HasActions::class);

        $wireAction = WireAction::make('test-action')
            ->livewire($component)
            ->label('Test Action')
            ->icon('heroicon-o-plus')
            ->visible(true);

        expect($wireAction)->toBeInstanceOf(WireAction::class);
    });

    it('can create bulk wire actions', function () {
        $component = \Mockery::mock(HasActions::class);

        $actionsConfig = [
            [
                'label' => 'Create Action',
                'action' => 'create',
                'icon' => 'heroicon-o-plus',
            ],
            [
                'label' => 'Edit Action',
                'action' => 'edit',
                'icon' => 'heroicon-o-pencil',
                'visible' => true,
            ],
            [
                'label' => 'Hidden Action',
                'action' => 'hidden',
                'visible' => false, // Should be filtered out
            ]
        ];

        $actions = WireAction::bulk($component, $actionsConfig);

        // Should return 2 actions (third is hidden)
        expect($actions)->toHaveCount(2)
            ->and($actions[0])->toBeInstanceOf(CrumbAction::class)
            ->and($actions[1])->toBeInstanceOf(CrumbAction::class);
    });

    it('filters out invisible actions in bulk creation', function () {
        $component = \Mockery::mock(HasActions::class);

        $actionsConfig = [
            [
                'label' => 'Visible Action',
                'action' => 'visible',
                'visible' => true,
            ],
            [
                'label' => 'Hidden Action 1',
                'action' => 'hidden1',
                'visible' => false,
            ],
            [
                'label' => 'Hidden Action 2',
                'action' => 'hidden2',
                'visible' => false,
            ]
        ];

        $actions = WireAction::bulk($component, $actionsConfig);

        expect($actions)->toHaveCount(1);
    });

    it('can execute wire actions when visible', function () {
        $component = \Mockery::mock(HasActions::class);

        $wireAction = WireAction::make('test')
            ->livewire($component)
            ->visible(true);

        $result = $wireAction->execute('testAction');

        expect($result)->toBeInstanceOf(CrumbAction::class);
    });

    it('returns null when executing invisible wire actions', function () {
        $component = \Mockery::mock(HasActions::class);

        $wireAction = WireAction::make('test')
            ->livewire($component)
            ->visible(false);

        $result = $wireAction->execute('testAction');

        expect($result)->toBeNull();
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