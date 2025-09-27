<?php

/**
 * Demo Usage Example - How to use the UserWireStep
 *
 * This shows a practical implementation of the new WireStep feature
 */

// 1. In your Livewire component or controller:

use Demo\Components\UserManagementCrumb;

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
        <!-- Render the breadcrumb with WireStep -->
        @livewire('demo.components.user-management-crumb', ['record' => $user])

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

// 3. Key Features Demonstrated:

/*
1. **Fluent API Usage:**
   UserWireStep::make('user-details')
       ->label($user->name)
       ->icon('heroicon-o-user')
       ->current(true)
       ->stepData(['user' => $user])

2. **Dynamic Actions:**
   - Actions visibility based on user permissions
   - Real-time form validation
   - Custom modal forms with Filament components

3. **State Management:**
   - Step data for passing context
   - Parent-child communication via events
   - Automatic refresh after actions

4. **Event Handling:**
   - 'user:updated' event from WireStep to parent
   - 'step:refresh' for updating step state
   - 'actioncrumb:action-executed' for action tracking

5. **Permission System:**
   - Role-based action visibility
   - Dynamic enable/disable states
   - Security checks before action execution

6. **Integration with Filament:**
   - Full form component support
   - Notification system
   - Modal dialogs with custom content

7. **Mixed Step Types:**
   - Regular Steps for navigation
   - WireStep for advanced functionality
   - Seamless integration in same breadcrumb

Usage Benefits:
- Reusable components across different contexts
- Consistent UI/UX with existing actioncrumb system
- Full Livewire reactivity and state management
- Extensible permission and security system
- Easy to test and maintain
*/