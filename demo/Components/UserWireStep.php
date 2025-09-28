<?php

declare(strict_types=1);

namespace Demo\Components;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Livewire\Component;
use Hdaklue\Actioncrumb\Traits\HasCrumbSteps;
use Hdaklue\Actioncrumb\Step;
use Hdaklue\Actioncrumb\Action as CrumbAction;

/**
 * Demo UserStepComponent - Livewire component for user management step
 *
 * This component is embedded as a WireStep in breadcrumbs using:
 * WireStep::make(UserStepComponent::class, ['user' => $user])
 *
 * Features demonstrated:
 * - Full Livewire component embedded as breadcrumb step
 * - Custom Filament Actions with forms
 * - State management with component properties
 * - Parent communication via events
 * - Dynamic breadcrumb actions
 */
class UserStepComponent extends Component implements HasActions, HasForms
{
    use HasCrumbSteps;
    use InteractsWithActions;
    use InteractsWithForms;

    public ?object $user = null;
    public string $userRole = 'viewer';
    public ?object $department = null;

    public function mount(?object $user = null, string $userRole = 'viewer', ?object $department = null): void
    {
        $this->user = $user ?? $this->createDemoUser();
        $this->userRole = $userRole;
        $this->department = $department ?? $this->createDemoDepartment();
    }

    protected function crumbSteps(): array
    {
        return [
            Step::make('user-details')
                ->label($this->user->name ?? 'User Details')
                ->icon('heroicon-o-user')
                ->current(true)
                ->actions([
                    CrumbAction::make('edit-user')
                        ->label('Edit User')
                        ->icon('heroicon-o-pencil')
                        ->visible(fn() => $this->user && $this->canEditUser())
                        ->execute(fn() => $this->mountAction('editUser')),

                    CrumbAction::make('change-role')
                        ->label('Change Role')
                        ->icon('heroicon-o-shield-check')
                        ->visible(fn() => $this->user && $this->canManageRoles())
                        ->execute(fn() => $this->mountAction('changeRole')),

                    CrumbAction::make('send-message')
                        ->label('Send Message')
                        ->icon('heroicon-o-envelope')
                        ->visible(fn() => $this->user)
                        ->execute(fn() => $this->mountAction('sendMessage')),

                    CrumbAction::make('view-activity')
                        ->label('View Activity')
                        ->icon('heroicon-o-clock')
                        ->visible(fn() => $this->user && $this->canViewActivity())
                        ->execute(fn() => $this->mountAction('viewActivity')),

                    CrumbAction::make('delete-user')
                        ->label('Delete User')
                        ->icon('heroicon-o-trash')
                        ->visible(fn() => $this->user && $this->canDeleteUser())
                        ->execute(fn() => $this->mountAction('deleteUser')),
                ])
        ];
    }

    public function editUserAction(): Action
    {
        return Action::make('editUser')
            ->label('Edit User')
            ->modalHeading('Edit User Details')
            ->form([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(20),
            ])
            ->fillForm([
                'name' => $this->user->name ?? '',
                'email' => $this->user->email ?? '',
                'phone' => $this->user->phone ?? '',
            ])
            ->action(function (array $data) {
                // Simulate user update
                $this->user->name = $data['name'];
                $this->user->email = $data['email'];
                $this->user->phone = $data['phone'] ?? null;

                Notification::make()
                    ->title('User updated successfully')
                    ->body("Updated {$data['name']}'s information")
                    ->success()
                    ->send();

                // Refresh the breadcrumb steps
                $this->refreshCrumbSteps();

                // Notify parent component
                $this->dispatch('user:updated', ['userId' => $this->user->id, 'name' => $data['name']]);
            });
    }

    public function changeRoleAction(): Action
    {
        return Action::make('changeRole')
            ->label('Change User Role')
            ->modalHeading('Change User Role')
            ->form([
                Select::make('role')
                    ->label('New Role')
                    ->options([
                        'viewer' => 'Viewer',
                        'editor' => 'Editor',
                        'admin' => 'Administrator',
                        'super_admin' => 'Super Administrator',
                    ])
                    ->required()
                    ->native(false),

                Textarea::make('reason')
                    ->label('Reason for Change')
                    ->placeholder('Explain why you are changing this user\'s role...')
                    ->rows(3),
            ])
            ->fillForm([
                'role' => $this->userRole,
            ])
            ->action(function (array $data) {
                $oldRole = $this->userRole;
                $this->userRole = $data['role'];

                Notification::make()
                    ->title('Role changed successfully')
                    ->body("Changed from {$oldRole} to {$data['role']}")
                    ->success()
                    ->send();

                // Log the role change
                if (function_exists('logger')) {
                    logger()->info('User role changed', [
                        'user_id' => $this->user->id,
                        'old_role' => $oldRole,
                        'new_role' => $data['role'],
                        'reason' => $data['reason'] ?? 'No reason provided',
                        'changed_by' => auth()->id(),
                    ]);
                }

                $this->refreshCrumbSteps();
            });
    }

    public function sendMessageAction(): Action
    {
        return Action::make('sendMessage')
            ->label('Send Message')
            ->modalHeading('Send Message to ' . ($this->user->name ?? 'User'))
            ->form([
                TextInput::make('subject')
                    ->label('Subject')
                    ->required()
                    ->maxLength(255),

                Textarea::make('message')
                    ->label('Message')
                    ->required()
                    ->rows(5)
                    ->placeholder('Write your message here...'),
            ])
            ->action(function (array $data) {
                // Simulate sending message
                Notification::make()
                    ->title('Message sent successfully')
                    ->body("Sent '{$data['subject']}' to {$this->user->name}")
                    ->success()
                    ->send();

                // Log message
                if (function_exists('logger')) {
                    logger()->info('Message sent to user', [
                        'recipient_id' => $this->user->id,
                        'subject' => $data['subject'],
                        'sender_id' => auth()->id(),
                    ]);
                }
            });
    }

    public function viewActivityAction(): Action
    {
        return Action::make('viewActivity')
            ->label('View Activity Log')
            ->modalHeading('Activity Log for ' . ($this->user->name ?? 'User'))
            ->modalContent(view('demo.user-activity', ['user' => $this->user]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->action(function () {
                // Just close the modal
            });
    }

    public function deleteUserAction(): Action
    {
        return Action::make('deleteUser')
            ->label('Delete User')
            ->modalHeading('Delete User')
            ->modalDescription('Are you sure you want to delete this user? This action cannot be undone.')
            ->requiresConfirmation()
            ->color('danger')
            ->action(function () {
                $userName = $this->user->name;

                // Simulate user deletion
                Notification::make()
                    ->title('User deleted successfully')
                    ->body("Deleted user: {$userName}")
                    ->success()
                    ->send();

                // Navigate away after deletion
                return redirect()->route('users.index');
            });
    }

    // Permission methods for demo purposes
    protected function canEditUser(): bool
    {
        return in_array($this->userRole, ['admin', 'super_admin']);
    }

    protected function canManageRoles(): bool
    {
        return $this->userRole === 'super_admin';
    }

    protected function canViewActivity(): bool
    {
        return in_array($this->userRole, ['admin', 'super_admin']);
    }

    protected function canDeleteUser(): bool
    {
        return $this->userRole === 'super_admin' && $this->user->id !== auth()->id();
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

    public function render()
    {
        return view('demo.user-step-component', [
            'user' => $this->user,
            'userRole' => $this->userRole,
            'department' => $this->department,
        ]);
    }
}