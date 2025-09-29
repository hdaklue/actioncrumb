{{-- Step Actions Component - Renders only the actions/dropdown portion for WireStep components --}}
@if (count($actions) > 0)
    <div x-data="{
        open: false,
        showStepActionsModal: false,
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
        showMobileModal() {
            this.showStepActionsModal = true;
        },
        closeMobileModal() {
            this.showStepActionsModal = false;
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
            @click="showMobileModal()"
            class="{{ $config->getMobileButtonClasses() }}">
            <x-icon name="heroicon-o-ellipsis-vertical" class="h-5 w-5" />
        </button>

        {{-- Desktop: Use dropdown --}}
        <button x-show="!isMobile" x-cloak
            x-ref="dropdownButton"
            @click="toggleDropdown()"
            class="{{ $config->getDropdownTriggerClasses(false) }}">
            <x-icon name="heroicon-o-chevron-down" class="h-4 w-4" />
        </button>

        {{-- Desktop Dropdown --}}
        <div x-show="open && !isMobile" x-cloak
            x-ref="dropdown"
            class="actioncrumb-dropdown-portal {{ $config->getDropdownMenuClasses() }} fixed z-50"
            @click.stop=""
            style="position: fixed;">
            @foreach ($actions as $actionIndex => $action)
                @if ($action->isVisible())
                    @if ($action->hasSeparator() && !$loop->first)
                        <hr class="{{ $config->getDropdownSeparatorClasses() }}">
                    @endif

                    <button
                        @click="$wire.handleActioncrumbAction('{{ md5($stepLabel . $action->getLabel() . $actionIndex) }}', '{{ md5($stepLabel) }}'); closeDropdown()"
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

        {{-- Mobile Modal --}}
        <div
            x-show="showStepActionsModal" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click="closeMobileModal()"
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
                            Actions
                        </h3>
                        <button
                            @click="closeMobileModal()"
                            class="{{ $config->getMobileModalCloseButtonClasses() }}">
                            <x-icon name="heroicon-o-x-mark" class="w-6 h-6" />
                        </button>
                    </div>

                    {{-- Actions List --}}
                    <div class="space-y-1">
                        @foreach($actions as $actionIndex => $action)
                            @if($action->isVisible())
                                @if($action->hasSeparator() && !$loop->first)
                                    <hr class="{{ $config->getMobileModalSeparatorClasses() }}">
                                @endif

                                <button
                                    @click="$wire.handleActioncrumbAction('{{ md5($stepLabel . $action->getLabel() . $actionIndex) }}', '{{ md5($stepLabel) }}'); closeMobileModal()"
                                    class="{{ $config->getMobileModalActionItemClasses($action->isEnabled()) }}"
                                    {{ !$action->isEnabled() ? 'disabled' : '' }}>

                                    @if($action->getIcon())
                                        <x-icon name="{{ $action->getIcon() }}" class="w-5 h-5 me-3 text-{{ $config->getSecondaryColor()->value }}-400 flex-shrink-0" />
                                    @else
                                        <x-icon name="heroicon-o-squares-2x2" class="w-5 h-5 me-3 text-{{ $config->getSecondaryColor()->value }}-400 flex-shrink-0" />
                                    @endif

                                    <span class="font-medium flex-1 flex items-center text-start">{{ $action->getLabel() }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>

                    {{-- Empty State --}}
                    @if(count($actions) === 0)
                        <div class="text-center py-8 text-{{ $config->getSecondaryColor()->value }}-500 dark:text-{{ $config->getSecondaryColor()->value }}-400">
                            No actions available
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif