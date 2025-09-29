<?php

use Hdaklue\Actioncrumb\Components\WireCrumb;
use Hdaklue\Actioncrumb\{Step, Action};
use Hdaklue\Actioncrumb\Support\WireAction;
use Hdaklue\Actioncrumb\Traits\HasActionCrumbs;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Livewire\Component;

// Test Livewire component for WireAction testing
class TestWireActionComponent extends Component implements HasActions
{
    use InteractsWithActions;
    use HasActionCrumbs;

    protected function actioncrumbs(): array
    {
        return [
            Action::make('Regular Action')->execute(fn() => 'regular'),
        ];
    }

    public function testAction()
    {
        return \Filament\Actions\Action::make('test')
            ->action(fn() => 'test executed');
    }
}

// Test WireCrumb component
class TestThreeLevelCrumb extends WireCrumb
{
    protected function crumbSteps(): array
    {
        $wireActionComponent = new TestWireActionComponent();
        $wireAction = WireAction::make('test-action')->livewire($wireActionComponent);

        return [
            // Level 2: Regular Step with mixed Action types
            Step::make('Dashboard')
                ->icon('heroicon-o-home')
                ->url('/dashboard')
                ->actions([
                    // Level 3: Regular Action
                    Action::make('Export')->execute(fn() => 'exported'),
                    // Level 3: WireAction (should be converted to Action)
                    $wireAction->execute('test'),
                ]),

            // Level 2: Current step
            Step::make('Current')->current(),
        ];
    }
}

describe('Three-Level Architecture', function () {
    it('supports Level 1: WireCrumb holds steps and renders them', function () {
        $crumb = new TestThreeLevelCrumb();

        // Test that renderCrumbSteps works (this calls getProcessedSteps internally)
        $rendered = $crumb->renderCrumbSteps();

        expect($rendered)->toBeString()
            ->and($rendered)->not->toBeEmpty();
    });

    it('supports Level 2: Steps can have mixed Action and WireAction objects', function () {
        $wireActionComponent = new TestWireActionComponent();
        $wireAction = WireAction::make('test-action')->livewire($wireActionComponent);

        // Create Step with mixed action types
        $step = Step::make('Test Step')->actions([
            Action::make('Regular Action')->execute(fn() => 'regular'),
            $wireAction->execute('test'), // WireAction.execute() returns Action
        ]);

        $actions = $step->getActions();

        expect($actions)->toHaveCount(2)
            ->and($actions[0])->toBeInstanceOf(Action::class)
            ->and($actions[1])->toBeInstanceOf(Action::class); // WireAction.execute() returns Action
    });

    it('supports Level 3: WireAction has livewire() and execute() methods', function () {
        $wireActionComponent = new TestWireActionComponent();
        $wireAction = WireAction::make('test-action')
            ->livewire($wireActionComponent)
            ->label('Test Action')
            ->icon('heroicon-o-test');

        // Execute should return an Action object
        $action = $wireAction->execute('test');

        expect($action)->toBeInstanceOf(Action::class)
            ->and($action->getLabel())->toBe('Test Action')
            ->and($action->getIcon())->toBe('heroicon-o-test');
    });

    it('rejects raw WireAction objects (without execute() call)', function () {
        $wireActionComponent = new TestWireActionComponent();
        $wireAction = WireAction::make('test-action')->livewire($wireActionComponent);

        expect(fn() => Step::make('Test')->actions([$wireAction]))
            ->toThrow(\InvalidArgumentException::class, 'WireAction objects cannot be used directly');
    });

    it('properly integrates all three levels together', function () {
        $crumb = new TestThreeLevelCrumb();
        $rendered = $crumb->renderCrumbSteps();

        // Should render without errors
        expect($rendered)->toBeString()
            ->and($rendered)->not->toBeEmpty();
    });

    it('handles WireAction visibility correctly in Step actions', function () {
        $wireActionComponent = new TestWireActionComponent();

        // Visible WireAction
        $visibleWireAction = WireAction::make('visible')
            ->livewire($wireActionComponent)
            ->visible(true);

        // Invisible WireAction
        $invisibleWireAction = WireAction::make('invisible')
            ->livewire($wireActionComponent)
            ->visible(false);

        $step = Step::make('Test')->actions([
            Action::make('Regular')->execute(fn() => 'regular'),
            $visibleWireAction->execute('test'),
            $invisibleWireAction->execute('test'), // Returns null, should be filtered
        ]);

        $actions = $step->getActions();

        // Should have 2 actions (regular + visible WireAction)
        // Invisible WireAction.execute() returns null and gets filtered
        expect($actions)->toHaveCount(2);
    });
});