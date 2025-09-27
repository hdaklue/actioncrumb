<?php

declare(strict_types=1);

namespace Demo\Components;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Hdaklue\Actioncrumb\Components\WireStep;
use Hdaklue\Actioncrumb\Support\WireAction;

/**
 * Demo UserWireStep - Shows WireStep capabilities
 *
 * Features demonstrated:
 * - Custom Filament Actions with forms
 * - State management with stepData
 * - Parent communication
 * - Dynamic visibility and enabled states
 * - Event handling and refresh
 */
class UserWireStep extends WireStep
{
    public ?object $user = null;
    public string $userRole = 'viewer';

    public function mount(
        string $stepId,
        string|\Closure|null $label = null,
        ?string $icon = null,
        string|\Closure|null $url = null,
        ?string $route = null,
        array $routeParams = [],
        bool $current = false,
        bool|\Closure $visible = true,
        bool|\Closure $enabled = true,
        ?\Filament\Actions\Contracts\HasActions $parent = null,
        array $stepData = []
    ): void {
        parent::mount($stepId, $label, $icon, $url, $route, $routeParams, $current, $visible, $enabled, $parent, $stepData);

        // Load user from step data
        $this->user = $stepData['user'] ?? null;
        $this->userRole = $stepData['userRole'] ?? 'viewer';
    }

    protected function actioncrumbs(): array
    {
        return [
            WireAction::make('edit-user')
                ->label('Edit User')
                ->icon('heroicon-o-pencil')
                ->livewire($this)
                ->visible(fn() => $this->user && $this->canEditUser())
                ->execute('editUser'),

            WireAction::make('change-role')
                ->label('Change Role')
                ->icon('heroicon-o-shield-check')
                ->livewire($this)
                ->visible(fn() => $this->user && $this->canManageRoles())
                ->execute('changeRole'),

            WireAction::make('send-message')
                ->label('Send Message')
                ->icon('heroicon-o-envelope')
                ->livewire($this)
                ->visible(fn() => $this->user)
                ->execute('sendMessage'),

            WireAction::make('view-activity')
                ->label('View Activity')
                ->icon('heroicon-o-clock')
                ->livewire($this)
                ->visible(fn() => $this->user && $this->canViewActivity())
                ->execute('viewActivity'),

            WireAction::make('delete-user')
                ->label('Delete User')
                ->icon('heroicon-o-trash')
                ->livewire($this)
                ->visible(fn() => $this->user && $this->canDeleteUser())
                ->execute('deleteUser'),
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

                // Refresh the step to update the label
                $this->refreshStep();

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

                // Update step data
                $this->setStepData('userRole', $data['role']);

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

                $this->refreshStep();
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

    // Override to show dynamic label
    public function getLabel(): string
    {
        if ($this->user) {
            return $this->user->name . " ({$this->userRole})";
        }

        return parent::getLabel();
    }
}