<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-white dark:bg-gray-900 text-gray-900 dark:text-white min-h-screen">
    {{-- Demo Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">ActionCrumb Demo</h1>
        <p class="text-gray-600 dark:text-gray-400">
            Interactive demonstration of the ActionCrumb package with mobile responsiveness.
        </p>
    </div>

    {{-- ActionCrumb Component --}}
    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg mb-8">
        <h2 class="text-lg font-semibold mb-4">Current Breadcrumb:</h2>
        {!! $this->renderActioncrumbs() !!}
    </div>

    {{-- Demo Controls --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- User Selection --}}
        <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">Select User:</h3>
            <div class="space-y-2">
                @foreach($users as $user)
                    <button 
                        wire:click="selectUser({{ $user['id'] }})"
                        class="w-full text-left px-4 py-3 rounded-lg border {{ $selectedUser && $selectedUser['id'] === $user['id'] ? 'bg-blue-100 dark:bg-blue-900 border-blue-300 dark:border-blue-600' : 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }} transition-colors">
                        <div class="font-medium">{{ $user['name'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user['email'] }}</div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Mobile Testing Tips --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-blue-900 dark:text-blue-100">Mobile Testing:</h3>
            <div class="space-y-3 text-sm text-blue-800 dark:text-blue-200">
                <div class="flex items-start space-x-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>Default Mode:</strong> Full breadcrumb with auto-scroll to current step</span>
                </div>
                <div class="flex items-start space-x-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>Mobile Actions:</strong> Dropdown actions automatically use modals</span>
                </div>
                <div class="flex items-start space-x-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>Text Wrapping:</strong> Long labels wrap naturally (e.g., "User Management")</span>
                </div>
                <div class="flex items-start space-x-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span><strong>Touch Targets:</strong> 44px minimum height for iOS guidelines</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Feature Showcase --}}
    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg">
        <h3 class="text-lg font-semibold mb-4">Features Demonstrated:</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <h4 class="font-medium mb-2 text-green-600 dark:text-green-400">✅ Responsive Design</h4>
                <ul class="space-y-1 text-gray-600 dark:text-gray-400">
                    <li>• Mobile auto-scroll</li>
                    <li>• Touch-friendly targets</li>
                    <li>• Text wrapping</li>
                    <li>• Modal actions on mobile</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-2 text-blue-600 dark:text-blue-400">🎨 Theming</h4>
                <ul class="space-y-1 text-gray-600 dark:text-gray-400">
                    <li>• Dark mode support</li>
                    <li>• RTL language support</li>
                    <li>• Configurable colors</li>
                    <li>• Multiple theme styles</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium mb-2 text-purple-600 dark:text-purple-400">⚡ Functionality</h4>
                <ul class="space-y-1 text-gray-600 dark:text-gray-400">
                    <li>• Dynamic visibility</li>
                    <li>• Conditional enabling</li>
                    <li>• Action execution</li>
                    <li>• Livewire integration</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Notifications --}}
    <div 
        x-data="{ 
            show: false, 
            message: '',
            showNotification(event) {
                this.message = event.detail;
                this.show = true;
                setTimeout(() => this.show = false, 3000);
            }
        }"
        @notify.window="showNotification($event)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg"
        style="display: none;">
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span x-text="message"></span>
        </div>
    </div>
</div>