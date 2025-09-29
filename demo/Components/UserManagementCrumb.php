<?php

declare(strict_types=1);

namespace Demo\Components;

use Hdaklue\Actioncrumb\Components\WireCrumb;
use Hdaklue\Actioncrumb\Components\WireStep;
use Hdaklue\Actioncrumb\Step;

/**
 * Demo UserManagementCrumb - Shows mixed Step and WireStep usage
 */
class UserManagementCrumb extends WireCrumb
{
    public ?object $user = null;
    public ?object $department = null;
    public string $currentUserRole = 'viewer';

    public function mount($record = null, $parent = null)
    {
        parent::mount($record, $parent);

        // Simulate loading user data
        $this->user = $record ?? $this->createDemoUser();
        $this->department = $this->user->department ?? $this->createDemoDepartment();
        $this->currentUserRole = auth()->user()->role ?? 'admin';
    }

    protected function crumbSteps(): array
    {
        return [
            // Regular Step for navigation
            Step::make('dashboard')
                ->label('Dashboard')
                ->route('dashboard')
                ->icon('heroicon-o-home'),

            // Regular Step for departments
            Step::make('departments')
                ->label('Departments')
                ->route('departments.index')
                ->icon('heroicon-o-building-office'),

            // Regular Step for current department
            Step::make('department')
                ->label($this->department->name ?? 'Department')
                ->route('departments.show', ['department' => $this->department->id ?? 1])
                ->icon('heroicon-o-user-group'),

            // Regular Step for users list
            Step::make('users')
                ->label('Users')
                ->route('users.index')
                ->icon('heroicon-o-users'),

            // WireStep for advanced user management - embeds a Livewire component
            WireStep::make(UserStepComponent::class, [
                    'user' => $this->user,
                    'userRole' => $this->currentUserRole,
                    'department' => $this->department,
                ])
                ->label($this->user->name ?? 'User Details')
                ->icon('heroicon-o-user')
                ->current(true),
        ];
    }

    // Listener for user updates from the WireStep
    protected function getListeners(): array
    {
        return array_merge(parent::getListeners(), [
            'user:updated' => 'handleUserUpdated',
        ]);
    }

    public function handleUserUpdated($data)
    {
        // Handle user update from WireStep
        $this->user->name = $data['name'];

        // Refresh the crumb steps to update the label
        $this->refreshCrumbSteps();

        \Filament\Notifications\Notification::make()
            ->title('Breadcrumb Updated')
            ->body('User information updated in breadcrumb')
            ->info()
            ->send();
    }

    // Demo data creation methods
    private function createDemoUser(): object
    {
        return (object) [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1 (555) 123-4567',
            'role' => 'editor',
            'department_id' => 1,
            'created_at' => now(),
            'last_login' => now()->subHours(2),
        ];
    }

    private function createDemoDepartment(): object
    {
        return (object) [
            'id' => 1,
            'name' => 'Engineering',
            'description' => 'Software Development Team',
            'manager_id' => 2,
            'user_count' => 15,
        ];
    }
}