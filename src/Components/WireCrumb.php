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
        return view('hdaklue.actioncrumb::components.wire-crumb', [
            'renderedActioncrumbs' => $this->renderActioncrumbs()
        ]);
    }


    /**
     * Define the actioncrumbs for this component.
     * Must be implemented by child classes.
     *
     * @return array Array of Step or WireStep instances with optional Action instances
     */
    abstract protected function actioncrumbs(): array;

    /**
     * Override getActioncrumbs to process WireSteps
     *
     * @return array Array of Step or WireStep instances
     */
    public function getActioncrumbs(): array
    {
        if (empty($this->actioncrumbSteps)) {
            $steps = $this->actioncrumbs();
            $this->actioncrumbSteps = $this->processSteps($steps);
        }

        return $this->actioncrumbSteps;
    }

    /**
     * Process steps to handle WireStep instances
     */
    protected function processSteps(array $steps): array
    {
        $processedSteps = [];

        foreach ($steps as $step) {
            if ($step instanceof \Hdaklue\Actioncrumb\Components\WireStep) {
                // Ensure WireStep has parent reference
                if (!$step->parent) {
                    $step->parent($this);
                }
                $processedSteps[] = $step;
            } elseif ($step instanceof \Hdaklue\Actioncrumb\Step) {
                $processedSteps[] = $step;
            } else {
                // Invalid step type - log warning and skip
                if (function_exists('logger')) {
                    logger()->warning('Invalid step type in actioncrumbs', [
                        'type' => get_class($step),
                        'component' => get_class($this)
                    ]);
                }
            }
        }

        return $processedSteps;
    }

    /**
     * Check if any steps are WireSteps
     */
    public function hasWireSteps(): bool
    {
        foreach ($this->getActioncrumbs() as $step) {
            if ($step instanceof \Hdaklue\Actioncrumb\Components\WireStep) {
                return true;
            }
        }
        return false;
    }
}