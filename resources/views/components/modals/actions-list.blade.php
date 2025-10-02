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
    <div class="{{ $config->getMobileModalBackdropClasses() }}"></div>
    
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
            class="{{ $config->getMobileModalContainerClasses() }}">
            
            {{-- Header with Back Button --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <button
                        @click="showActionsModal = false; showBreadcrumbModal = true"
                        class="{{ $config->getMobileModalCloseButtonClasses() }}">
                        <x-icon name="heroicon-o-arrow-left" class="w-5 h-5" />
                    </button>
                    <h3 class="{{ $config->getMobileModalHeaderClasses() }}" x-text="currentStepLabel || 'Actions'">
                        Actions
                    </h3>
                </div>
                <button
                    @click="showActionsModal = false"
                    class="{{ $config->getMobileModalCloseButtonClasses() }}">
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
                                        <hr class="{{ $config->getMobileModalSeparatorClasses() }}">
                                    @endif
                                    
                                    <button 
                                        @click="{{ $action->isEnabled() ? '$wire.handleActioncrumbAction(\'' . md5($step->getLabel() . $action->getLabel() . $actionIndex) . '\', \'' . md5($step->getLabel()) . '\'); showActionsModal = false' : '' }}"
                                        class="{{ $config->getMobileModalActionItemClasses($action->isEnabled()) }}"
                                        {{ !$action->isEnabled() ? 'disabled' : '' }}>
                                        
                                        @if($action->getIcon())
                                            <x-icon name="{{ $action->getIcon() }}" class="w-4 h-4 text-{{ $config->getSecondaryColor()->value }}-400 flex-shrink-0" />
                                        @else
                                            <x-icon name="heroicon-o-squares-2x2" class="w-4 h-4 text-{{ $config->getSecondaryColor()->value }}-400 flex-shrink-0" />
                                        @endif

                                        <span class="font-medium flex-1 flex items-center text-start">{{ $action->getLabel() }}</span>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </template>
                @endforeach
            </div>
            
            {{-- Empty State --}}
            <div x-show="currentStep === null" x-cloak class="text-center py-8 text-{{ $config->getSecondaryColor()->value }}-500 dark:text-{{ $config->getSecondaryColor()->value }}-400">
                No actions available
            </div>
        </div>
    </div>
</div>