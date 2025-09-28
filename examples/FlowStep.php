<?php

declare(strict_types=1);

namespace App\Livewire\Steps;

use Hdaklue\Actioncrumb\Traits\HasActionCrumbs;
use Hdaklue\Actioncrumb\Action;
use Livewire\Component;

/**
 * Example FlowStep - A simple Livewire component that can be used as a step
 */
class FlowStep extends Component
{
    use HasActionCrumbs;

    public string $flowId;
    public string $flowName = 'My Flow';

    public function mount(string $flowId, string $flowName = 'My Flow')
    {
        $this->flowId = $flowId;
        $this->flowName = $flowName;
    }

    protected function actioncrumbs(): array
    {
        return [
            Action::make('create-task')
                ->label('Create Task')
                ->icon('heroicon-o-plus')
                ->execute(fn() => $this->createTask()),

            Action::make('edit-flow')
                ->label('Edit Flow')
                ->icon('heroicon-o-pencil')
                ->execute(fn() => $this->editFlow()),

            Action::make('duplicate-flow')
                ->label('Duplicate Flow')
                ->icon('heroicon-o-document-duplicate')
                ->execute(fn() => $this->duplicateFlow()),
        ];
    }

    public function createTask()
    {
        // Simulate task creation
        $this->dispatch('notify', message: "Task created for flow: {$this->flowName}");
    }

    public function editFlow()
    {
        // Simulate flow editing
        $this->dispatch('notify', message: "Editing flow: {$this->flowName}");
    }

    public function duplicateFlow()
    {
        // Simulate flow duplication
        $this->dispatch('notify', message: "Duplicated flow: {$this->flowName}");
    }

    public function render()
    {
        return view('livewire.steps.flow-step', [
            'renderedActioncrumbs' => $this->renderActioncrumbs()
        ]);
    }
}