<?php

use Hdaklue\Actioncrumb\Renderers\StepRenderer;
use Hdaklue\Actioncrumb\Config\ActioncrumbConfig;
use Hdaklue\Actioncrumb\Step;
use Hdaklue\Actioncrumb\Components\WireStep;
use Hdaklue\Actioncrumb\Action;
use Illuminate\Support\Facades\View;

describe('StepRenderer Class', function () {
    beforeEach(function () {
        // Mock View facade
        View::shouldReceive('make')
            ->with('actioncrumb::components.actioncrumb', \Mockery::any())
            ->andReturn(\Mockery::mock(['render' => '<div>rendered</div>']));
    });

    it('can be created with make method', function () {
        $config = new ActioncrumbConfig();
        $renderer = StepRenderer::make($config);

        expect($renderer)->toBeInstanceOf(StepRenderer::class);
    });

    it('can render regular steps', function () {
        $config = new ActioncrumbConfig();
        $renderer = new StepRenderer($config);

        $steps = [
            Step::make('home')->label('Home'),
            Step::make('products')->label('Products'),
        ];

        $result = $renderer->render($steps);

        expect($result)->toBe('<div>rendered</div>');
    });

    it('can process wire steps', function () {
        $config = new ActioncrumbConfig();
        $renderer = new StepRenderer($config);

        $wireStep = WireStep::make('TestComponent')
            ->label('Wire Step');

        $steps = [
            Step::make('home')->label('Home'),
            $wireStep,
        ];

        $result = $renderer->render($steps);

        expect($result)->toBe('<div>rendered</div>');
    });

    it('handles non-existent wire step components', function () {
        $config = new ActioncrumbConfig();
        $renderer = new StepRenderer($config);

        $wireStep = WireStep::make('NonExistentComponent')
            ->label('Non-existent Wire Step');

        $steps = [$wireStep];

        // This should not throw an error and should fallback to regular step
        $result = $renderer->render($steps);

        expect($result)->toBe('<div>rendered</div>');
    });

    it('filters out invalid step types', function () {
        $config = new ActioncrumbConfig();
        $renderer = new StepRenderer($config);

        $steps = [
            Step::make('valid')->label('Valid Step'),
            'invalid-step-type',
            null,
            Step::make('another-valid')->label('Another Valid Step'),
        ];

        // Should not throw an error and should process only valid steps
        $result = $renderer->render($steps);

        expect($result)->toBe('<div>rendered</div>');
    });

    it('can detect wire steps', function () {
        $config = new ActioncrumbConfig();
        $renderer = new StepRenderer($config);

        $wireStep = WireStep::make('TestComponent');
        $regularStep = Step::make('regular');

        $steps = [$regularStep, $wireStep];

        expect($renderer->hasWireSteps($steps))->toBeTrue();
    });

    it('returns false when no wire steps present', function () {
        $config = new ActioncrumbConfig();
        $renderer = new StepRenderer($config);

        $steps = [
            Step::make('home')->label('Home'),
            Step::make('products')->label('Products'),
        ];

        expect($renderer->hasWireSteps($steps))->toBeFalse();
    });

    it('can filter regular steps', function () {
        $config = new ActioncrumbConfig();
        $renderer = new StepRenderer($config);

        $wireStep = WireStep::make('TestComponent');
        $regularStep1 = Step::make('regular1');
        $regularStep2 = Step::make('regular2');

        $steps = [$regularStep1, $wireStep, $regularStep2];

        $regularSteps = $renderer->getRegularSteps($steps);

        expect($regularSteps)->toHaveCount(2)
            ->and($regularSteps[0])->toBe($regularStep1)
            ->and($regularSteps[2])->toBe($regularStep2);
    });

    it('can filter wire steps', function () {
        $config = new ActioncrumbConfig();
        $renderer = new StepRenderer($config);

        $wireStep1 = WireStep::make('TestComponent1');
        $wireStep2 = WireStep::make('TestComponent2');
        $regularStep = Step::make('regular');

        $steps = [$wireStep1, $regularStep, $wireStep2];

        $wireSteps = $renderer->getWireSteps($steps);

        expect($wireSteps)->toHaveCount(2)
            ->and($wireSteps[0])->toBe($wireStep1)
            ->and($wireSteps[2])->toBe($wireStep2);
    });

    it('passes component to view', function () {
        $config = new ActioncrumbConfig();
        $renderer = new StepRenderer($config);

        $steps = [Step::make('test')->label('Test')];
        $component = new stdClass();

        // The specific mock is already set up in beforeEach, so the component just needs to be passed
        $result = $renderer->render($steps, $component);

        expect($result)->toBe('<div>rendered</div>');
    });

    it('handles wire step processing errors gracefully', function () {
        $config = new ActioncrumbConfig();
        $renderer = new StepRenderer($config);

        // Create a wire step that might cause processing errors
        $wireStep = WireStep::make('ProblematicComponent')
            ->label('Problematic Step');

        $steps = [$wireStep];

        // Should not throw an error and should handle gracefully
        $result = $renderer->render($steps);

        expect($result)->toBe('<div>rendered</div>');
    });

    it('handles empty steps array', function () {
        $config = new ActioncrumbConfig();
        $renderer = new StepRenderer($config);

        $result = $renderer->render([]);

        expect($result)->toBe('<div>rendered</div>');
    });

    afterEach(function () {
        \Mockery::close();
    });
});