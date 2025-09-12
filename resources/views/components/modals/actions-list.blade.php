{{-- Mobile Actions Modal --}}
<div 
    x-show="showActionsModal" x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    @click="showActionsModal = false"
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
                    Actions
                </h3>
                <button 
                    @click="showActionsModal = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <x-icon name="heroicon-o-x-mark" class="w-6 h-6" />
                </button>
            </div>
            
            {{-- Actions List --}}
            <div class="space-y-1">
                @foreach($steps as $stepIndex => $step)
                    <template x-if="currentStep === {{ $stepIndex }} && {{ count($step->getActions()) }} > 0">
                        <div>
                            @foreach($step->getActions() as $actionIndex => $action)
                                @if($action->isVisible())
                                    @if($action->hasSeparator() && !$loop->first)
                                        <hr class="my-2 border-gray-200 dark:border-gray-600">
                                    @endif
                                    
                                    <button 
                                        @click="$wire.handleActioncrumbAction('{{ md5($step->getLabel() . $action->getLabel() . $actionIndex) }}', '{{ md5($step->getLabel()) }}'); showActionsModal = false"
                                        class="w-full flex items-center px-3 py-3 text-left text-gray-700 dark:text-gray-200 rounded-lg transition-colors {{ !$action->isEnabled() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                                        {{ !$action->isEnabled() ? 'disabled' : '' }}>
                                        
                                        @if($action->getIcon())
                                            <x-icon name="{{ $action->getIcon() }}" class="w-5 h-5 mr-3 text-gray-400 flex-shrink-0" />
                                        @else
                                            <x-icon name="heroicon-o-squares-2x2" class="w-5 h-5 mr-3 text-gray-400 flex-shrink-0" />
                                        @endif
                                        
                                        <span class="font-medium">{{ $action->getLabel() }}</span>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </template>
                @endforeach
            </div>
            
            {{-- Empty State --}}
            <div x-show="currentStep === null" x-cloak class="text-center py-8 text-gray-500 dark:text-gray-400">
                No actions available
            </div>
        </div>
    </div>
</div>