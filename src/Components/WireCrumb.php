<?php

declare(strict_types=1);

namespace Hdaklue\Actioncrumb\Components;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Hdaklue\Actioncrumb\Traits\HasCrumbSteps;
use Livewire\Component;

/**
 * WireCrumb - Base Livewire component for breadcrumb factories with Filament actions
 * 
 * This abstract component provides the foundation for creating breadcrumb components
 * that can execute Filament Actions. It includes all necessary traits and interfaces
 * to work seamlessly with both hdaklue/actioncrumb and Filament.
 *
 */
abstract class WireCrumb extends Component implements HasActions, HasSchemas
{
    use HasCrumbSteps;
    use InteractsWithActions;
    use InteractsWithSchemas;


    /**
     * Render the component view.
     * Override this method to specify a custom view.
     */
    public function render()
    {
        return view('actioncrumb::components.wire-crumb', [
            'renderedCrumbSteps' => $this->renderCrumbSteps()
        ]);
    }

    /**
     * Define the crumb steps for this component.
     * Must be implemented by child classes.
     *
     * @return array Array of Step or WireStep instances
     */
    abstract protected function crumbSteps(): array;
}