<?php

use Hdaklue\Actioncrumb\Components\WireCrumb;
use Hdaklue\Actioncrumb\Step;
use Hdaklue\Actioncrumb\Action;
use Hdaklue\Actioncrumb\Components\WireStep;

// Test implementation of WireCrumb for testing
class TestWireCrumb extends WireCrumb
{
    public array $steps = [];

    public function crumbSteps(): array
    {
        return $this->steps;
    }

    public function setSteps(array $steps): void
    {
        $this->steps = $steps;
    }
}

describe('WireCrumb Component Structure', function () {
    it('can be instantiated', function () {
        $crumb = new TestWireCrumb();

        expect($crumb)->toBeInstanceOf(WireCrumb::class)
            ->and($crumb->steps)->toBeArray();
    });

    it('can set and get steps', function () {
        $crumb = new TestWireCrumb();
        $steps = [
            Step::make('home')->label('Home'),
            Step::make('products')->label('Products'),
        ];

        $crumb->setSteps($steps);

        expect($crumb->steps)->toHaveCount(2)
            ->and($crumb->steps[0])->toBeInstanceOf(Step::class)
            ->and($crumb->steps[1])->toBeInstanceOf(Step::class);
    });

    it('can handle mixed step types', function () {
        $crumb = new TestWireCrumb();

        $wireStep = WireStep::make('TestComponent')
            ->label('Wire Step');

        $regularStep = Step::make('regular')
            ->label('Regular Step');

        $steps = [$regularStep, $wireStep];
        $crumb->setSteps($steps);

        expect($crumb->steps)->toHaveCount(2)
            ->and($crumb->steps[0])->toBeInstanceOf(Step::class)
            ->and($crumb->steps[1])->toBeInstanceOf(WireStep::class);
    });

    it('handles step actions in data structure', function () {
        $crumb = new TestWireCrumb();

        $action = Action::make('edit')
            ->label('Edit')
            ->execute(fn() => 'edited');

        $step = Step::make('home')
            ->label('Home')
            ->actions([$action]);

        $crumb->setSteps([$step]);

        expect($crumb->steps[0]->hasActions())->toBeTrue()
            ->and($crumb->steps[0]->getActions())->toHaveCount(1);
    });

    it('can handle empty steps array', function () {
        $crumb = new TestWireCrumb();
        $crumb->setSteps([]);

        expect($crumb->steps)->toBeEmpty();
    });

    it('can access crumb steps method', function () {
        $crumb = new TestWireCrumb();
        $steps = [Step::make('test')->label('Test')];
        $crumb->setSteps($steps);

        $retrievedSteps = $crumb->crumbSteps();

        expect($retrievedSteps)->toHaveCount(1)
            ->and($retrievedSteps[0])->toBeInstanceOf(Step::class);
    });
});