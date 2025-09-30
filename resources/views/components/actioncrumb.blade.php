@php
    $isMobileDevice = false;
    $shouldUseCompactMenu = false;

    try {
        if (app()->bound(\Hdaklue\Actioncrumb\Services\MobileDetector::class)) {
            $mobileDetector = app(\Hdaklue\Actioncrumb\Services\MobileDetector::class);
            $isMobileDevice = $mobileDetector->isMobileOrTablet();
        } else {
            // Fallback detection
            $userAgent = request()->userAgent() ?? '';
            $isMobileDevice = (bool) preg_match(
                '/Mobile|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i',
                $userAgent,
            );
        }
        $shouldUseCompactMenu = $config->isCompactMenuOnMobile() && $isMobileDevice;
    } catch (\Exception $e) {
        // Safe fallback - disable compact menu on any error
        $isMobileDevice = false;
        $shouldUseCompactMenu = false;
    }
@endphp

<div x-data="{
    showBreadcrumbModal: false,
    showActionsModal: false,
    currentStep: null,
    isMobileDevice: @js($isMobileDevice),
    isCompactMenu: @js($shouldUseCompactMenu)
}" class="actioncrumb-container overflow-y-visible {{ !$config->isBackgroundEnabled() ? 'no-background' : '' }}" data-primary-color="{{ $config->getPrimaryColor()->value }}" data-secondary-color="{{ $config->getSecondaryColor()->value }}">

    @if ($shouldUseCompactMenu)
        {{-- Mobile Modal View --}}
        <nav class="{{ $config->getContainerClasses() }} justify-between" role="navigation" aria-label="Breadcrumb">
            {{-- Current/Last Step --}}
            @php $lastVisibleStep = collect($steps)->filter(fn($s) => $s->isVisible())->last(); @endphp
            @if ($lastVisibleStep)
                <div class="{{ $config->getMobileCurrentStepContainerClasses() }}">
                    @if ($lastVisibleStep->getIcon())
                        <x-icon name="{{ $lastVisibleStep->getIcon() }}"
                            class="me-2 h-4 w-4 flex-shrink-10 text-{{ $config->getSecondaryColor()->value }}-500" />
                    @endif
                    <span class="{{ $config->getCurrentStepMobileLabelClasses() }}">
                        {{ $lastVisibleStep->getLabel() }}
                    </span>
                </div>

                {{-- Navigation Button --}}
                <button @click="showBreadcrumbModal = true"
                    class="{{ $config->getMobileButtonClasses() }}">
                    <x-icon name="heroicon-o-bars-3" class="h-5 w-5" />
                </button>
            @endif
        </nav>

        {{-- Modals --}}
        @include('actioncrumb::components.modals.breadcrumb-list')
    @else
        {{-- Desktop/Default Horizontal Scroll View --}}
        <nav x-data="{
            isMobile: window.innerWidth <= 768,
            scrollToEnd() {
                if (this.isMobile && this.$el.scrollWidth > this.$el.clientWidth) {
                    // Scroll to show the current/last breadcrumb
                    this.$el.scrollTo({
                        left: this.$el.scrollWidth - this.$el.clientWidth,
                        behavior: 'smooth'
                    });
                }
            },
            handleResize() {
                this.isMobile = window.innerWidth <= 768;
                this.scrollToEnd();
            }
        }" x-init="// Initial scroll after content loads
        $nextTick(() => {
            setTimeout(() => scrollToEnd(), 100);
        });

        // Handle window resize
        window.addEventListener('resize', () => handleResize());

        // Handle device orientation change
        window.addEventListener('orientationchange', () => {
            setTimeout(() => {
                handleResize();
            }, 200);
        });"
            class="{{ $config->getContainerClasses() }} scrollbar-hide overflow-x-auto overflow-y-visible"
            role="navigation" aria-label="Breadcrumb">
            @foreach ($steps as $index => $step)
                @if ($step->isVisible())
                    <div class="flex flex-shrink-0 items-center">
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
                                            if (!this.isMobile) this.open = false; // Close dropdown when switching to mobile
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
                                        
                                        // Get button position relative to viewport
                                        const buttonRect = button.getBoundingClientRect();
                                        const viewportWidth = window.innerWidth;
                                        const viewportHeight = window.innerHeight;
                                        
                                        // Position dropdown below button
                                        let top = buttonRect.bottom + 4;
                                        let left = buttonRect.left;
                                        
                                        // Apply initial position
                                        dropdown.style.top = `${top}px`;
                                        dropdown.style.left = `${left}px`;
                                        dropdown.style.right = 'auto';
                                        dropdown.style.bottom = 'auto';
                                        
                                        // Check positioning after initial placement
                                        this.$nextTick(() => {
                                            const dropdownRect = dropdown.getBoundingClientRect();
                                            
                                            // Adjust horizontal position if off-screen
                                            if (dropdownRect.right > viewportWidth - 10) {
                                                dropdown.style.left = 'auto';
                                                dropdown.style.right = `${viewportWidth - buttonRect.right}px`;
                                            }
                                            
                                            // Adjust vertical position if off-screen
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
                                        @click="currentStep = {{ $index }}; showActionsModal = true"
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
                    </div>

                    @if (!$loop->last)
                        {{-- Configurable Separator --}}
                        <div class="flex items-center justify-center">
                            {!! $config->getSeparatorType()->getSvg() !!}
                        </div>
                    @endif
                @endif
            @endforeach
        </nav>

    @endif

    {{-- Always include modals for mobile dropdown actions regardless of compactMenuOnMobile setting --}}
    @include('actioncrumb::components.modals.actions-list')
</div>
