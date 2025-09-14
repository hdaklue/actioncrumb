<?php

declare(strict_types=1);

namespace Hdaklue\Actioncrumb\Components;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Hdaklue\Actioncrumb\Traits\HasActionCrumbs;
use Livewire\Component;

/**
 * WireCrumb - Base Livewire component for breadcrumb factories with Filament actions
 * 
 * This abstract component provides the foundation for creating breadcrumb components
 * that can execute Filament Actions. It includes all necessary traits and interfaces
 * to work seamlessly with both hdaklue/actioncrumb and Filament.
 * 
 * Usage:
 * ```php
 * class DocumentCrumb extends WireCrumb
 * {
 *     public ?Document $document = null;
 * 
 *     public function mount($record = null, $parent = null)
 *     {
 *         parent::mount($record, $parent);
 *         $this->document = $record;
 *     }
 * 
 *     protected function actioncrumbs(): array
 *     {
 *         return [
 *             Step::make('Documents')->actions([
 *                 WireAction::make('Create')
 *                     ->livewire($this)
 *                     ->execute('createDocument'),
 *             ]),
 *         ];
 *     }
 * 
 *     public function createDocumentAction(): Action
 *     {
 *         return Action::make('createDocument')
 *             ->form([...])
 *             ->action(fn($data) => Document::create($data));
 *     }
 * }
 * ```
 */
abstract class WireCrumb extends Component implements HasActions, HasSchemas
{
    use HasActionCrumbs;
    use InteractsWithActions;
    use InteractsWithSchemas;

    protected ?HasActions $parent = null;

    /**
     * Static factory method for creating instances.
     */
    public static function make(?HasActions $parent = null): static
    {
        $instance = new static();
        $instance->parent = $parent;

        return $instance;
    }

    /**
     * Mount the component with optional record and parent.
     * Override this method in child classes to handle specific records.
     */
    public function mount($record = null, $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Render the component view.
     * Override this method to specify a custom view.
     */
    public function render()
    {
        return view('hdaklue.actioncrumb::components.wire-crumb');
    }

    /**
     * Define the actioncrumbs for this component.
     * Must be implemented by child classes.
     * 
     * @return array Array of Step instances with optional Action instances
     */
    abstract protected function actioncrumbs(): array;
}