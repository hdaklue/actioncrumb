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
            
            {{-- Header --}}
            <div class="flex items-center justify-between mb-4">
                <h3 class="{{ $config->getMobileModalHeaderClasses() }}">
                    Navigation
                </h3>
                <button 
                    @click="showBreadcrumbModal = false"
                    class="{{ $config->getMobileModalCloseButtonClasses() }}">
                    <x-icon name="heroicon-o-x-mark" class="w-6 h-6" />
                </button>
            </div>
            
            {{-- Breadcrumb List --}}
            <nav class="space-y-2">
                @foreach($steps as $index => $step)
                    @if($step->isVisible())
                        <div class="{{ $config->getMobileModalItemClasses() }}">
                            <div class="{{ $config->getMobileModalItemContentClasses() }}">
                                @if($step->getIcon())
                                    <x-icon name="{{ $step->getIcon() }}" class="{{ $config->getMobileModalIconClasses() }}" />
                                @endif
                                
                                @if($step->isClickable())
                                    <a 
                                        href="{{ $step->getResolvedUrl() }}" 
                                        wire:navigate
                                        @click="showBreadcrumbModal = false"
                                        class="{{ $config->getMobileStepClasses($step->isClickable(), $step->isCurrent()) }}">
                                        {{ $step->getLabel() }}
                                    </a>
                                @else
                                    <span class="{{ $config->getMobileStepClasses($step->isClickable(), $step->isCurrent()) }}">
                                        {{ $step->getLabel() }}
                                    </span>
                                @endif
                            </div>
                            
                            {{-- Action Button --}}
                            @if($step->hasActions() && $config->isDropdownsEnabled())
                                <button 
                                    @click="currentStep = {{ $index }}; showBreadcrumbModal = false; showActionsModal = true"
                                    class="{{ $config->getMobileModalActionButtonClasses() }}">
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