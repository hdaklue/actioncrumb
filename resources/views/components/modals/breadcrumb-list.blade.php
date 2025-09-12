{{-- Mobile Breadcrumb List Modal --}}
<div 
    x-show="showBreadcrumbModal" x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    @click="showBreadcrumbModal = false"
    style="display: none;">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
    
    {{-- Modal Content --}}
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div 
            @click.stop
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all w-full max-w-full mx-4 sm:my-8 sm:w-full sm:max-w-sm sm:p-6 sm:mx-0">
            
            {{-- Header --}}
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                    Navigation
                </h3>
                <button 
                    @click="showBreadcrumbModal = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <x-icon name="heroicon-o-x-mark" class="w-6 h-6" />
                </button>
            </div>
            
            {{-- Breadcrumb List --}}
            <nav class="space-y-2">
                @foreach($steps as $index => $step)
                    @if($step->isVisible())
                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-center flex-1">
                                @if($step->getIcon())
                                    <x-icon name="{{ $step->getIcon() }}" class="w-5 h-5 mr-3 text-gray-400 flex-shrink-0" />
                                @endif
                                
                                @if($step->isClickable())
                                    <a 
                                        href="{{ $step->getResolvedUrl() }}" 
                                        wire:navigate
                                        @click="showBreadcrumbModal = false"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                        {{ $step->getLabel() }}
                                    </a>
                                @else
                                    <span class="text-gray-900 dark:text-gray-100 font-medium {{ $step->isCurrent() ? 'font-semibold' : '' }}">
                                        {{ $step->getLabel() }}
                                    </span>
                                @endif
                            </div>
                            
                            {{-- Action Button --}}
                            @if($step->hasActions() && $config->isDropdownsEnabled())
                                <button 
                                    @click="currentStep = {{ $index }}; showActionsModal = true"
                                    class="ml-3 p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-full transition-colors">
                                    <x-icon name="heroicon-o-ellipsis-vertical" class="w-5 h-5" />
                                </button>
                            @endif
                        </div>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>
</div>