<?php

use Hdaklue\Actioncrumb\ValueObjects\Step;
use Hdaklue\Actioncrumb\ValueObjects\Action;
use Hdaklue\Actioncrumb\Config\ActioncrumbConfig;
use Hdaklue\Actioncrumb\Enums\ThemeStyle;

// Example usage showcasing visible() and enabled() functionality
class ExampleComponent extends \Livewire\Component
{
    public bool $canEdit = true;
    public bool $showAdminActions = false;

    protected function actioncrumbs(): array
    {
        return [
            Step::make('Dashboard')
                ->route('dashboard')
                ->icon('heroicon-o-home'),

            Step::make('Users')
                ->route('users.index')
                ->actions([
                    Action::make('Create User')
                        ->route('users.create')
                        ->icon('heroicon-o-plus')
                        ->enabled($this->canEdit), // Only enabled if user can edit

                    Action::make('Import Users')
                        ->execute(fn() => $this->import())
                        ->icon('heroicon-o-document-arrow-up')
                        ->visible($this->showAdminActions) // Only visible to admins
                        ->enabled($this->canEdit),

                    Action::make('Export Users')
                        ->url('/users/export')
                        ->icon('heroicon-o-document-arrow-down')
                        ->separator() // Add separator before this action
                        ->visible(fn() => auth()->user()->can('export-users')) // Dynamic visibility
                ]),

            Step::make('John Doe')
                ->current()
                ->visible(fn() => !is_null($this->selectedUser)) // Only show if user selected
                ->actions([
                    Action::make('Edit Profile')
                        ->route('users.edit', ['user' => 1])
                        ->enabled(fn() => $this->canEdit && !$this->isUserSuspended()),

                    Action::make('Delete User')
                        ->execute(fn() => $this->deleteUser())
                        ->icon('heroicon-o-trash')
                        ->enabled(fn() => auth()->user()->can('delete-users'))
                        ->visible(fn() => !$this->isCurrentUser()), // Don't show delete for current user
                ])
        ];
    }

    public function render()
    {
        return view('livewire.example-component');
    }

    // Example of configuring compact mode in service provider or component
    protected function configureCompactMode()
    {
        ActioncrumbConfig::make()
            ->themeStyle(ThemeStyle::Simple)
            ->compact(true) // Enable compact spacing
            ->darkMode(false)
            ->bind();
    }

    private function import()
    {
        // Handle import logic here
        $this->dispatch('user-imported');
    }

    private function deleteUser()
    {
        // Developer is responsible for authorization within the closure
        if (!auth()->user()->can('delete-users')) {
            abort(403);
        }
        
        // Handle deletion logic here
        $this->dispatch('user-deleted');
    }

    private function isUserSuspended(): bool
    {
        return false; // Your logic here
    }

    private function isCurrentUser(): bool
    {
        return false; // Your logic here
    }
}