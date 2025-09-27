@php
    $isMobileDevice = false;
    $shouldUseCompactMenu = false;

    try {
        if (app()->bound(\Hdaklue\Actioncrumb\Services\MobileDetector::class)) {
            $mobileDetector = app(\Hdaklue\Actioncrumb\Services\MobileDetector::class);
            $isMobileDevice = $mobileDetector->isMobileOrTablet();
        } else {
            $userAgent = request()->userAgent() ?? '';
            $isMobileDevice = (bool) preg_match(
                '/Mobile|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i',
                $userAgent,
            );
        }
        $shouldUseCompactMenu = $config->isCompactMenuOnMobile() && $isMobileDevice;
    } catch (\Exception $e) {
        $isMobileDevice = false;
        $shouldUseCompactMenu = false;
    }
@endphp

<div x-data="{
    showActionsModal: false,
    isMobileDevice: @js($isMobileDevice),
    isCompactMenu: @js($shouldUseCompactMenu)
}" wire:key="step-{{ $step->getId() }}" class="step-component flex flex-shrink-0 items-center">

    @if ($step->isVisible())
        @if ($step->hasActions() && $config->isDropdownsEnabled())
            {{-- Step with dropdown actions - separate link and dropdown --}}
            <div class="flex flex-shrink-0 items-center {{ $config->getStepContainerClasses($step->isCurrent()) }}">
                {{-- Step link --}}
                @if ($step->isClickable())
                    <a href="{{ $step->getResolvedUrl() }}"
                        class="{{ $config->getStepClasses($step->isClickable(), $step->isCurrent(), true) }}"
                        wire:navigate>
                        @if ($step->getIcon())
                            <x-icon name="{{ $step->getIcon() }}"
                                class="me-2 h-5 w-5 flex-shrink-0" />
                        @endif
                        {{ $step->getLabel() }}
                    </a>
                @else
                    <span
                        class="{{ $config->getStepClasses($step->isClickable(), $step->isCurrent(), true) }}">
                        @if ($step->getIcon())
                            <x-icon name="{{ $step->getIcon() }}"
                                class="me-2 h-5 w-5 flex-shrink-0" />
                        @endif
                        {{ $step->getLabel() }}
                    </span>
                @endif

                {{-- Dropdown arrow (separate from link) --}}
                <div x-data="{
                    open: false,
                    isMobile: window.innerWidth <= 768,
                    init() {
                        window.addEventListener('resize', () => {
                            this.isMobile = window.innerWidth <= 768;
                            if (!this.isMobile) this.open = false;
                            if (this.open) this.positionDropdown();
                        });

                        window.addEventListener('scroll', () => {
                            if (this.open) this.positionDropdown();
                        });
                    },
                    toggleDropdown() {
                        this.open = !this.open;
                        if (this.open) {
                            this.$nextTick(() => this.positionDropdown());
                        }
                    },
                    closeDropdown() {
                        this.open = false;
                    },
                    positionDropdown() {
                        if (!this.open || this.isMobile) return;

                        const dropdown = this.$refs.dropdown;
                        const button = this.$refs.dropdownButton;
                        if (!dropdown || !button) return;

                        const buttonRect = button.getBoundingClientRect();
                        const viewportWidth = window.innerWidth;
                        const viewportHeight = window.innerHeight;

                        let top = buttonRect.bottom + 4;
                        let left = buttonRect.left;

                        dropdown.style.top = `${top}px`;
                        dropdown.style.left = `${left}px`;
                        dropdown.style.right = 'auto';
                        dropdown.style.bottom = 'auto';

                        this.$nextTick(() => {
                            const dropdownRect = dropdown.getBoundingClientRect();

                            if (dropdownRect.right > viewportWidth - 10) {
                                dropdown.style.left = 'auto';
                                dropdown.style.right = `${viewportWidth - buttonRect.right}px`;
                            }

                            if (dropdownRect.bottom > viewportHeight - 10) {
                                dropdown.style.top = 'auto';
                                dropdown.style.bottom = `${viewportHeight - buttonRect.top + 4}px`;
                            }
                        });
                    }
                }"
                @click.away="closeDropdown()"
                class="relative ms-1">
                    {{-- Mobile: Use modal button --}}
                    <button x-show="isMobile" x-cloak
                        @click="showActionsModal = true"
                        class="{{ $config->getMobileButtonClasses() }}">
                        <x-icon name="heroicon-o-ellipsis-vertical" class="h-5 w-5" />
                    </button>

                    {{-- Desktop: Use dropdown --}}
                    <button x-show="!isMobile" x-cloak
                        x-ref="dropdownButton"
                        @click="toggleDropdown()"
                        class="{{ $config->getDropdownTriggerClasses($step->isCurrent()) }}">
                        <x-icon name="heroicon-o-chevron-down"
                            class="h-4 w-4" />
                    </button>

                    <div x-show="open && !isMobile" x-cloak
                        x-ref="dropdown"
                        class="actioncrumb-dropdown-portal {{ $config->getDropdownMenuClasses() }} fixed z-50"
                        @click.stop=""
                        style="position: fixed;">
                        @foreach ($step->getActions() as $actionIndex => $action)
                            @if ($action->isVisible())
                                @if ($action->hasSeparator() && !$loop->first)
                                    <hr class="{{ $config->getDropdownSeparatorClasses() }}">
                                @endif

                                <button
                                    wire:click="handleActioncrumbAction('{{ md5($step->getLabel() . $action->getLabel() . $actionIndex) }}', '{{ md5($step->getLabel()) }}')"
                                    @click.stop="closeDropdown()"
                                    class="{{ $config->getDropdownItemClasses() }} {{ !$action->isEnabled() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ !$action->isEnabled() ? 'disabled' : '' }}>
                                    @if ($action->getIcon())
                                        <x-icon name="{{ $action->getIcon() }}"
                                            class="{{ $config->getActionIconClasses() }}" />
                                    @endif
                                    <span class="{{ $config->getDropdownItemTextClasses() }}">{{ $action->getLabel() }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif($step->isClickable())
            {{-- Clickable step without actions --}}
            <div class="{{ $config->getStepContainerClasses($step->isCurrent()) }}">
                <a href="{{ $step->getResolvedUrl() }}"
                    class="{{ $config->getStepClasses($step->isClickable(), $step->isCurrent()) }}"
                    wire:navigate>
                    @if ($step->getIcon())
                        <x-icon name="{{ $step->getIcon() }}"
                            class="mr-2 h-5 w-5 flex-shrink-0 rtl:ml-2 rtl:mr-0" />
                    @endif
                    {{ $step->getLabel() }}
                </a>
            </div>
        @else
            {{-- Current or inactive step --}}
            <div class="{{ $config->getStepContainerClasses($step->isCurrent()) }}">
                <span class="{{ $config->getStepClasses($step->isClickable(), $step->isCurrent()) }}">
                    @if ($step->getIcon())
                        <x-icon name="{{ $step->getIcon() }}"
                            class="mr-2 h-5 w-5 flex-shrink-0 rtl:ml-2 rtl:mr-0" />
                    @endif
                    {{ $step->getLabel() }}
                </span>
            </div>
        @endif

        {{-- Mobile Modal for Actions --}}
        @if ($step->hasActions())
            <div x-show="showActionsModal" x-cloak x-teleport="body"
                class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:p-0">
                <div x-show="showActionsModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="showActionsModal = false"></div>

                <div x-show="showActionsModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="w-full transform overflow-hidden rounded-t-lg bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-sm sm:rounded-lg">

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">
                                {{ $step->getLabel() }} Actions
                            </h3>
                            <button @click="showActionsModal = false"
                                class="rounded-md bg-white text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Close</span>
                                <x-icon name="heroicon-o-x-mark" class="h-6 w-6" />
                            </button>
                        </div>

                        <div class="mt-4 space-y-2">
                            @foreach ($step->getActions() as $actionIndex => $action)
                                @if ($action->isVisible())
                                    @if ($action->hasSeparator() && !$loop->first)
                                        <hr class="my-2 border-gray-200">
                                    @endif

                                    <button
                                        wire:click="handleActioncrumbAction('{{ md5($step->getLabel() . $action->getLabel() . $actionIndex) }}', '{{ md5($step->getLabel()) }}')"
                                        @click="showActionsModal = false"
                                        class="flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ !$action->isEnabled() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ !$action->isEnabled() ? 'disabled' : '' }}>
                                        @if ($action->getIcon())
                                            <x-icon name="{{ $action->getIcon() }}"
                                                class="mr-3 h-5 w-5 text-gray-400" />
                                        @endif
                                        <span>{{ $action->getLabel() }}</span>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>