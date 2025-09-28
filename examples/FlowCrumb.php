<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use Hdaklue\Actioncrumb\Components\WireCrumb;
use Hdaklue\Actioncrumb\Components\WireStep;
use Hdaklue\Actioncrumb\{Step, Action};
use App\Livewire\Steps\FlowStep;

/**
 * Example FlowCrumb - Shows the new architecture in action
 */
class FlowCrumb extends WireCrumb
{
    public string $flowId;
    public string $flowName;

    public function mount($record = null, $parent = null)
    {
        parent::mount($record, $parent);

        $this->flowId = $record['id'] ?? '123';
        $this->flowName = $record['name'] ?? 'Sample Flow';
    }

    protected function crumbSteps(): array
    {
        return [
            // Traditional Step with actions
            Step::make('Dashboard')
                ->label('Dashboard')
                ->icon('heroicon-o-home')
                ->url('/dashboard')
                ->actions([
                    Action::make('quick-stats')
                        ->label('Quick Stats')
                        ->icon('heroicon-o-chart-bar')
                        ->execute(fn() => $this->showQuickStats()),
                ]),

            // Traditional Step without actions
            Step::make('Flows')
                ->label('Flows')
                ->icon('heroicon-o-document-text')
                ->route('flows.index')
                ->actions([
                    Action::make('create-flow')
                        ->label('Create Flow')
                        ->icon('heroicon-o-plus')
                        ->execute(fn() => $this->createFlow()),

                    Action::make('import-flows')
                        ->label('Import Flows')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->execute(fn() => $this->importFlows()),
                ]),

            // WireStep transporter - loads external Livewire component
            WireStep::make(FlowStep::class, [
                'flowId' => $this->flowId,
                'flowName' => $this->flowName,
            ])
                ->label($this->flowName)
                ->icon('heroicon-o-cog-6-tooth')
                ->current()
                ->route('flows.show', ['flow' => $this->flowId]),
        ];
    }

    public function showQuickStats()
    {
        $this->dispatch('notify', message: 'Showing quick stats...');
    }

    public function createFlow()
    {
        $this->dispatch('notify', message: 'Creating new flow...');
    }

    public function importFlows()
    {
        $this->dispatch('notify', message: 'Importing flows...');
    }
}