<?php

namespace Hdaklue\Actioncrumb\Livewire;

use Livewire\Component;
use Hdaklue\Actioncrumb\Traits\HasActionCrumbs;
use Hdaklue\Actioncrumb\Step;
use Hdaklue\Actioncrumb\Action;

class ActioncrumbDemo extends Component
{
    use HasActionCrumbs;

    public array $users = [
        ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
        ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
        ['id' => 3, 'name' => 'Mike Johnson', 'email' => 'mike@example.com'],
    ];

    public ?array $selectedUser = null;

    public function mount()
    {
        // Simulate selecting a user for the demo
        $this->selectedUser = $this->users[0];
    }

    protected function actioncrumbs(): array
    {
        return [
            Step::make('Dashboard')
                ->route('dashboard')
                ->icon('heroicon-o-home'),

            Step::make('User Management')
                ->route('users.index')
                ->actions([
                    Action::make('Create User')
                        ->execute(fn() => $this->createUser())
                        ->icon('heroicon-o-plus')
                        ->enabled(true),

                    Action::make('Import Users')
                        ->execute(fn() => $this->importUsers())
                        ->icon('heroicon-o-document-arrow-up')
                        ->enabled(true),

                    Action::make('Export Users')
                        ->execute(fn() => $this->exportUsers())
                        ->icon('heroicon-o-document-arrow-down')
                        ->separator()
                        ->enabled(true),
                ]),

            Step::make($this->selectedUser ? $this->selectedUser['name'] : 'Select User')
                ->current()
                ->icon('heroicon-o-user')
                ->visible($this->selectedUser !== null)
                ->actions([
                    Action::make('Edit Profile')
                        ->execute(fn() => $this->editUser())
                        ->icon('heroicon-o-pencil')
                        ->enabled(true),

                    Action::make('View Details')
                        ->execute(fn() => $this->viewUser())
                        ->icon('heroicon-o-eye')
                        ->enabled(true),

                    Action::make('Delete User')
                        ->execute(fn() => $this->deleteUser())
                        ->icon('heroicon-o-trash')
                        ->separator()
                        ->enabled(true)
                        ->visible(true), // Always visible for demo
                ])
        ];
    }

    public function createUser()
    {
        $this->dispatch('notify', 'User creation dialog would open here');
    }

    public function importUsers()
    {
        $this->dispatch('notify', 'User import process would start here');
    }

    public function exportUsers()
    {
        $this->dispatch('notify', 'User export would download here');
    }

    public function editUser()
    {
        $this->dispatch('notify', 'Edit user form would open here');
    }

    public function viewUser()
    {
        $this->dispatch('notify', 'User details view would open here');
    }

    public function deleteUser()
    {
        $this->dispatch('notify', 'User deletion confirmation would show here');
    }

    public function selectUser($userId)
    {
        $this->selectedUser = collect($this->users)->firstWhere('id', $userId);
    }

    public function render()
    {
        return view('actioncrumb::livewire.actioncrumb-demo');
    }
}