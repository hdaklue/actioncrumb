<nav class="{{ $config->getContainerClasses() }}" role="navigation" aria-label="Breadcrumb">
    @foreach($steps as $index => $step)
        <div class="flex items-center">
            @if($step->hasActions() && $config->isDropdownsEnabled())
                {{-- Step with dropdown actions - separate link and dropdown --}}
                <div class="flex items-center">
                    {{-- Step link --}}
                    @if($step->isClickable())
                        <a 
                            href="{{ $step->getResolvedUrl() }}" 
                            class="{{ $config->getStepClasses($step->isClickable(), $step->isCurrent()) }}"
                            wire:navigate>
                            @if($step->getIcon())
                                <x-icon name="{{ $step->getIcon() }}" class="w-4 h-4 {{ $config->getDirection() === \Hdaklue\Actioncrumb\Enums\Direction::RTL ? 'ml-1' : 'mr-1' }}" />
                            @endif
                            {{ $step->getLabel() }}
                        </a>
                    @else
                        <span class="{{ $config->getStepClasses($step->isClickable(), $step->isCurrent()) }}">
                            @if($step->getIcon())
                                <x-icon name="{{ $step->getIcon() }}" class="w-4 h-4 {{ $config->getDirection() === \Hdaklue\Actioncrumb\Enums\Direction::RTL ? 'ml-1' : 'mr-1' }}" />
                            @endif
                            {{ $step->getLabel() }}
                        </span>
                    @endif
                    
                    {{-- Dropdown arrow (separate from link) --}}
                    <div x-data="{ open: false }" class="relative {{ $config->getDirection() === \Hdaklue\Actioncrumb\Enums\Direction::RTL ? 'mr-1' : 'ml-1' }}">
                        <button 
                            @click="open = !open" 
                            @click.away="open = false"
                            class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
                            <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                        </button>
                        
                        <div 
                            x-show="open" 
                            x-transition
                            class="{{ $config->getDropdownMenuClasses() }}"
                            @click="open = false">
                            @foreach($step->getActions() as $actionIndex => $action)
                                @if($action->hasSeparator() && !$loop->first)
                                    <hr class="my-1 border-gray-200 dark:border-gray-600">
                                @endif
                                
                                <button 
                                    wire:click="handleActioncrumbAction('{{ md5($step->getLabel() . $action->getLabel() . $actionIndex) }}', '{{ md5($step->getLabel()) }}')"
                                    class="{{ $config->getDropdownItemClasses() }}">
                                    @if($action->getIcon())
                                        <x-icon name="{{ $action->getIcon() }}" class="w-4 h-4 text-gray-400" />
                                    @endif
                                    <span>{{ $action->getLabel() }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @elseif($step->isClickable())
                {{-- Clickable step without actions --}}
                <a 
                    href="{{ $step->getResolvedUrl() }}" 
                    class="{{ $config->getStepClasses($step->isClickable(), $step->isCurrent()) }}"
                    wire:navigate>
                    @if($step->getIcon())
                        <svg class="w-4 h-4 {{ $config->getDirection() === \Hdaklue\Actioncrumb\Enums\Direction::RTL ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                    @endif
                    {{ $step->getLabel() }}
                </a>
            @else
                {{-- Current or inactive step --}}
                <span class="{{ $config->getStepClasses($step->isClickable(), $step->isCurrent()) }}">
                    @if($step->getIcon())
                        <svg class="w-4 h-4 {{ $config->getDirection() === \Hdaklue\Actioncrumb\Enums\Direction::RTL ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                    @endif
                    {{ $step->getLabel() }}
                </span>
            @endif
        </div>
        
        @if(!$loop->last)
            {{-- Configurable Separator --}}
            <div class="flex items-center justify-center">
                {!! $config->getSeparatorType()->getSvg() !!}
            </div>
        @endif
    @endforeach
</nav>