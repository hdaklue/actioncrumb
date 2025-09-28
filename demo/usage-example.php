<?php

/**
 * Demo Usage Example - How to use WireStep with embedded Livewire components
 *
 * This shows the new WireStep transporter implementation that embeds
 * existing Livewire components as breadcrumb steps
 */

// 1. In your Livewire component or controller:

use Demo\Components\UserManagementCrumb;
use Demo\Components\UserStepComponent;
use Hdaklue\Actioncrumb\Components\WireStep;

class UserController extends Controller
{
    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user,
            'breadcrumb' => UserManagementCrumb::make($user)
        ]);
    }
}

// 2. In your Blade view (users/show.blade.php):
?>

<x-app-layout>
    <div class="py-6">
        <!-- Option 1: Use WireCrumb (contains multiple steps including WireStep) -->
        @livewire('demo.components.user-management-crumb', ['record' => $user])

        <!-- Option 2: Embed UserStepComponent directly as WireStep -->
        <!-- @livewire('demo.components.user-step-component', ['user' => $user, 'userRole' => $user->role]) -->

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">User Information</h3>
                            <dl class="mt-2 text-sm text-gray-600">
                                <div class="py-2">
                                    <dt class="font-medium">Email:</dt>
                                    <dd>{{ $user->email }}</dd>
                                </div>
                                <div class="py-2">
                                    <dt class="font-medium">Phone:</dt>
                                    <dd>{{ $user->phone ?? 'Not provided' }}</dd>
                                </div>
                                <div class="py-2">
                                    <dt class="font-medium">Role:</dt>
                                    <dd class="capitalize">{{ $user->role }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Use the actions in the breadcrumb above to:
                            </p>
                            <ul class="mt-2 text-sm text-gray-600 list-disc list-inside">
                                <li>Edit user information</li>
                                <li>Change user role</li>
                                <li>Send messages</li>
                                <li>View activity log</li>
                                <li>Delete user (if authorized)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<?php

// 3. Alternative: Create WireStep programmatically in your component:

use Hdaklue\Actioncrumb\Traits\HasCrumbSteps;

class UserProfilePage extends Component
{
    use HasCrumbSteps;

    public User $user;

    protected function crumbSteps(): array
    {
        return [
            Step::make('dashboard')
                ->label('Dashboard')
                ->url('/dashboard'),

            Step::make('users')
                ->label('Users')
                ->route('users.index'),

            // Embed UserStepComponent as a WireStep
            WireStep::make(UserStepComponent::class, [
                'user' => $this->user,
                'userRole' => auth()->user()->role ?? 'viewer',
            ])
                ->label($this->user->name)
                ->icon('heroicon-o-user')
                ->current(true),
        ];
    }
}

// 4. Key Features Demonstrated:

/*
1. **WireStep Transporter Usage:**
   WireStep::make(UserStepComponent::class, ['user' => $user])
       ->label($user->name)
       ->icon('heroicon-o-user')
       ->current(true)

2. **Component Embedding:**
   - Full Livewire components embedded as breadcrumb steps
   - Component maintains its own state and lifecycle
   - Parameters passed to component mount method

3. **State Management:**
   - Component properties for state management
   - Parent-child communication via Livewire events
   - Independent component refresh capabilities

4. **Event Handling:**
   - 'user:updated' event from embedded component
   - 'crumb-steps:refreshed' for step updates
   - Standard Livewire event system

5. **Permission System:**
   - Role-based action visibility within component
   - Dynamic enable/disable states
   - Security checks at component level

6. **Integration with Filament:**
   - Full form component support within embedded component
   - Notification system
   - Modal dialogs with custom content

7. **Mixed Step Types:**
   - Regular Steps for navigation
   - WireStep for embedding Livewire components
   - Fallback to regular Step if component fails

8. **Component Reusability:**
   - Same component can be used in different contexts
   - Parameters control component behavior
   - Component can be used standalone or embedded

Usage Benefits:
- Embed any Livewire component as a breadcrumb step
- Full component lifecycle and state management
- Consistent UI/UX with existing actioncrumb system
- Fallback to regular Step if component fails
- Easy to test components independently
- Maximum flexibility for complex breadcrumb steps
*/