<?php

namespace Hdaklue\Actioncrumb\Configuration;

use Hdaklue\Actioncrumb\Enums\ThemeStyle;
use Hdaklue\Actioncrumb\Enums\SeparatorType;
use Hdaklue\Actioncrumb\Enums\TailwindColor;

class ActioncrumbConfig
{
    protected ThemeStyle $themeStyle = ThemeStyle::Simple;
    protected SeparatorType $separatorType = SeparatorType::Chevron;
    protected TailwindColor $primaryColor = TailwindColor::Blue;
    protected TailwindColor $secondaryColor = TailwindColor::Gray;
    protected bool $enableDropdowns = true;
    protected bool $compact = false;
    protected bool $compactMenuOnMobile = false;
    protected bool $enableBackground = true;
    protected array $themes = [];

    public static function make(): self
    {
        return new static();
    }

    public function themeStyle(ThemeStyle $style): self
    {
        $this->themeStyle = $style;
        return $this;
    }

    public function separatorType(SeparatorType $type): self
    {
        $this->separatorType = $type;
        return $this;
    }

    public function primaryColor(TailwindColor $color): self
    {
        $this->primaryColor = $color;
        return $this;
    }

    public function secondaryColor(TailwindColor $color): self
    {
        $this->secondaryColor = $color;
        return $this;
    }


    public function enableDropdowns(bool $enable = true): self
    {
        $this->enableDropdowns = $enable;
        return $this;
    }


    public function compact(bool $compact = true): self
    {
        $this->compact = $compact;
        return $this;
    }

    public function compactMenuOnMobile(bool $compactMenuOnMobile = true): self
    {
        $this->compactMenuOnMobile = $compactMenuOnMobile;
        return $this;
    }

    public function background(bool $enable = true): self
    {
        $this->enableBackground = $enable;
        return $this;
    }

    public function registerTheme(string $name, array $classes): self
    {
        $this->themes[$name] = $classes;
        return $this;
    }

    public function bind(): void
    {
        app()->singleton(ActioncrumbConfig::class, fn() => $this);
    }

    public function getThemeStyle(): ThemeStyle
    {
        return $this->themeStyle;
    }

    public function getSeparatorType(): SeparatorType
    {
        return $this->separatorType;
    }

    public function getPrimaryColor(): TailwindColor
    {
        return $this->primaryColor;
    }

    public function getSecondaryColor(): TailwindColor
    {
        return $this->secondaryColor;
    }


    public function isDropdownsEnabled(): bool
    {
        return $this->enableDropdowns;
    }


    public function isCompact(): bool
    {
        return $this->compact;
    }

    public function isCompactMenuOnMobile(): bool
    {
        return $this->compactMenuOnMobile;
    }

    public function isBackgroundEnabled(): bool
    {
        return $this->enableBackground;
    }

    public function getContainerClasses(): string
    {
        $baseClasses = 'flex items-center text-sm';
        $spacingClass = $this->compact ? 'space-x-1 rtl:space-x-reverse' : 'space-x-2 rtl:space-x-reverse';
        
        return "{$baseClasses} {$spacingClass}";
    }

    public function getMobileScrollClasses(): string
    {
        return 'flex items-center text-sm space-x-2 rtl:space-x-reverse overflow-x-auto scrollbar-hide';
    }

    public function getStepContainerClasses(bool $isCurrent): string
    {
        // Use our consolidated CSS classes - styling is now handled by CSS
        $baseClasses = 'actioncrumb-step';

        // Add theme-specific classes
        $themeClass = match ($this->themeStyle) {
            ThemeStyle::Simple => 'theme-simple',
            ThemeStyle::Rounded => 'theme-rounded',
            ThemeStyle::Square => 'theme-square',
        };

        // Add current state class
        $currentClass = $isCurrent ? 'is-current' : '';

        // Add slight padding for breathing room
        $paddingClasses = 'px-2 py-1';

        // Add background colors and borders if background is enabled
        $backgroundClasses = '';
        $borderClasses = '';

        if ($this->enableBackground) {
            if ($isCurrent) {
                // Use brighter secondary color for current step (neutral, not attention-grabbing)
                $backgroundClasses = "bg-{$this->secondaryColor->value}-100 dark:bg-{$this->secondaryColor->value}-800/50";

                // Add theme-specific border radius and border with dark mode support
                $borderClasses = match ($this->themeStyle) {
                    ThemeStyle::Simple => "rounded-md border-l-3 border-{$this->secondaryColor->value}-400 dark:border-{$this->secondaryColor->value}-500",
                    ThemeStyle::Rounded => "rounded-full border border-{$this->secondaryColor->value}-400 dark:border-{$this->secondaryColor->value}-500",
                    ThemeStyle::Square => "rounded-none border border-{$this->secondaryColor->value}-400 dark:border-{$this->secondaryColor->value}-500",
                };
            } else {
                // Use lighter secondary color for non-current steps
                $backgroundClasses = "bg-{$this->secondaryColor->value}-50 dark:bg-{$this->secondaryColor->value}-800/30";

                // Add theme-specific border radius and border with dark mode support
                $borderClasses = match ($this->themeStyle) {
                    ThemeStyle::Simple => "rounded-md border-l-3 border-{$this->secondaryColor->value}-200 dark:border-{$this->secondaryColor->value}-700",
                    ThemeStyle::Rounded => "rounded-full border border-{$this->secondaryColor->value}-200 dark:border-{$this->secondaryColor->value}-700",
                    ThemeStyle::Square => "rounded-none border border-{$this->secondaryColor->value}-200 dark:border-{$this->secondaryColor->value}-700",
                };
            }
        }

        return trim("{$baseClasses} {$themeClass} {$currentClass} {$paddingClasses} {$backgroundClasses} {$borderClasses}");
    }

    public function getStepClasses(bool $isClickable, bool $isCurrent, bool $hasDropdown = false): string
    {
        $classes = ['inline-flex items-center whitespace-nowrap'];

        // Apply color scheme based on current state and configuration
        if ($isCurrent) {
            // Current step uses primary color
            $classes[] = 'text-' . $this->primaryColor->value . '-600 dark:text-' . $this->primaryColor->value . '-400';
            $classes[] = 'font-medium';
        } else {
            // Other steps use secondary color
            $classes[] = 'text-' . $this->secondaryColor->value . '-600 dark:text-' . $this->secondaryColor->value . '-400';

            if ($isClickable) {
                // Add hover states for clickable steps
                $classes[] = 'hover:text-' . $this->primaryColor->value . '-700 dark:hover:text-' . $this->primaryColor->value . '-300';
                $classes[] = 'transition-colors duration-150';
                $classes[] = 'cursor-pointer';
            }
        }

        // Add padding based on compact mode
        $padding = $this->compact ? 'px-2 py-1' : 'px-3 py-2';
        $classes[] = $padding;

        return implode(' ', $classes);
    }

    public function getDropdownTriggerClasses(bool $isCurrent = false): string
    {
        // Basic dropdown trigger classes with proper color configuration
        $padding = $this->compact ? 'p-1' : 'p-1.5';

        // Apply dynamic background and border colors based on enableBackground
        $bgClasses = '';
        $borderClasses = '';

        if ($this->enableBackground) {
            $bgClasses = "bg-{$this->secondaryColor->value}-50 dark:bg-{$this->secondaryColor->value}-800/40";
            $borderClasses = match ($this->themeStyle) {
                ThemeStyle::Simple => "rounded-md border-l-3 border-{$this->secondaryColor->value}-300 dark:border-{$this->secondaryColor->value}-700",
                ThemeStyle::Rounded => "rounded-full border border-{$this->secondaryColor->value}-300 dark:border-{$this->secondaryColor->value}-700",
                ThemeStyle::Square => "rounded-none border border-{$this->secondaryColor->value}-300 dark:border-{$this->secondaryColor->value}-700",
            };
        } else {
            $borderClasses = 'rounded-md';
        }

        $classes = [
            'inline-flex',
            'items-center',
            $padding,
            'cursor-pointer',
            'text-' . $this->secondaryColor->value . '-500',
            'hover:text-' . $this->primaryColor->value . '-600',
            'dark:text-' . $this->secondaryColor->value . '-400',
            'dark:hover:text-' . $this->primaryColor->value . '-400',
            'hover:bg-' . $this->primaryColor->value . '-100',
            'dark:hover:bg-' . $this->primaryColor->value . '-800/40',
            'transition-colors',
            'duration-150',
            $bgClasses,
            $borderClasses
        ];

        // Add theme class for CSS targeting
        $themeClass = match ($this->themeStyle) {
            ThemeStyle::Simple => 'theme-simple',
            ThemeStyle::Rounded => 'theme-rounded',
            ThemeStyle::Square => 'theme-square',
        };

        $classes[] = $themeClass;

        return implode(' ', $classes);
    }

    public function getDropdownMenuClasses(): string
    {
        // Theme-specific border radius for modern minimal look
        $borderRadius = match ($this->themeStyle) {
            ThemeStyle::Simple => 'rounded-lg',    // Larger radius for modern look
            ThemeStyle::Rounded => 'rounded-2xl',  // Extra large radius for pill aesthetic
            ThemeStyle::Square => 'rounded-md',    // Subtle rounding for modern feel
        };

        // Modern shadow system with layered depth
        $shadow = 'shadow-xl ring-1 ring-black/5';

        // Enhanced dropdown with compact spacing
        $classes = [
            'bg-white dark:bg-gray-800',
            'border border-' . $this->secondaryColor->value . '-200 dark:border-' . $this->secondaryColor->value . '-700',
            $borderRadius,
            'min-w-48 max-w-80 max-h-80',
            'overflow-y-auto',
            $shadow,
            'py-1',  // Compact padding
            'backdrop-blur-sm',  // Subtle blur for depth
        ];

        return implode(' ', $classes);
    }

    public function getDropdownItemClasses(): string
    {
        // Theme-specific styling for dropdown items
        $itemRadius = match ($this->themeStyle) {
            ThemeStyle::Simple => 'mx-1 rounded-md',      // Inner spacing with rounded corners
            ThemeStyle::Rounded => 'mx-1 rounded-lg',     // Larger rounded corners for pill theme
            ThemeStyle::Square => 'mx-1 rounded-sm',      // Minimal rounding for square theme
        };

        // Compact dropdown items with smooth interactions
        $classes = [
            'flex items-center gap-2.5',
            'w-full text-start',
            'px-3 py-2',  // Compact padding
            'text-sm font-medium',
            'text-' . $this->secondaryColor->value . '-700 dark:text-' . $this->secondaryColor->value . '-200',
            'hover:bg-' . $this->primaryColor->value . '-50 hover:text-' . $this->primaryColor->value . '-700',
            'dark:hover:bg-' . $this->primaryColor->value . '-900/20 dark:hover:text-' . $this->primaryColor->value . '-300',
            'active:bg-' . $this->primaryColor->value . '-100 dark:active:bg-' . $this->primaryColor->value . '-800/30',
            'transition-colors duration-150',  // Simple color transitions only
            'cursor-pointer',
            $itemRadius,
        ];

        return implode(' ', $classes);
    }

    public function getDropdownItemTextClasses(): string
    {
        // Classes to make text take full available width in dropdown items with RTL support
        $classes = [
            'flex-1', // Take remaining space after icon
            'text-start', // Start alignment (supports RTL)
            'truncate', // Handle long text gracefully
        ];
        
        return implode(' ', $classes);
    }

    public function getMobileStepClasses(bool $isClickable, bool $isCurrent): string
    {
        $classes = ['inline-flex items-center'];
        
        // Apply color scheme based on current state and configuration
        if ($isCurrent) {
            // Current step uses primary color
            $classes[] = 'text-' . $this->primaryColor->value . '-600 dark:text-' . $this->primaryColor->value . '-400';
            $classes[] = 'font-semibold';
        } else {
            // Other steps use secondary color
            $classes[] = 'text-' . $this->secondaryColor->value . '-600 dark:text-' . $this->secondaryColor->value . '-400';
            
            if ($isClickable) {
                // Add hover states for clickable steps
                $classes[] = 'hover:text-' . $this->primaryColor->value . '-700 dark:hover:text-' . $this->primaryColor->value . '-300';
                $classes[] = 'transition-colors duration-150';
                $classes[] = 'font-medium';
            } else {
                $classes[] = 'font-medium';
            }
        }
        
        return implode(' ', $classes);
    }

    public function getMobileModalBackdropClasses(): string
    {
        // Enhanced backdrop with smooth blur effect and modern opacity
        return 'fixed inset-0 bg-gray-900/60 dark:bg-black/70 backdrop-blur-sm transition-all duration-200';
    }

    public function getMobileModalContainerClasses(): string
    {
        // Theme-specific border radius and visual accents
        $borderRadius = match ($this->themeStyle) {
            ThemeStyle::Simple => 'rounded-xl',      // Smooth modern corners
            ThemeStyle::Rounded => 'rounded-2xl',    // Extra rounded for pill aesthetic
            ThemeStyle::Square => 'rounded-md',      // Subtle rounding for modern feel
        };

        // Modern shadow system with subtle elevation
        $shadow = 'shadow-2xl ring-1 ring-black/5';

        // Enhanced container with modern spacing and borders
        return "relative transform overflow-hidden {$borderRadius} bg-white dark:bg-gray-800 {$shadow} px-4 pb-4 pt-5 text-start transition-all duration-200 w-full max-w-full mx-4 sm:my-8 sm:w-full sm:max-w-sm sm:p-6 sm:mx-0 border border-{$this->secondaryColor->value}-200 dark:border-{$this->secondaryColor->value}-700";
    }

    public function getMobileModalItemClasses(bool $isCurrent = false): string
    {
        // Theme-specific border radius with modern spacing
        $borderRadius = match ($this->themeStyle) {
            ThemeStyle::Simple => 'rounded-lg',
            ThemeStyle::Rounded => 'rounded-xl',
            ThemeStyle::Square => 'rounded-md',  // Add subtle rounding for modern feel
        };

        // Apply background and border styling similar to desktop steps when item is current
        if ($isCurrent) {
            $backgroundClasses = "bg-{$this->secondaryColor->value}-100 dark:bg-{$this->secondaryColor->value}-800/50";
            $borderClasses = match ($this->themeStyle) {
                ThemeStyle::Simple => "border-l-3 border-{$this->secondaryColor->value}-400 dark:border-{$this->secondaryColor->value}-500",
                ThemeStyle::Rounded => "border border-{$this->secondaryColor->value}-400 dark:border-{$this->secondaryColor->value}-500",
                ThemeStyle::Square => "border border-{$this->secondaryColor->value}-400 dark:border-{$this->secondaryColor->value}-500",
            };
            return "flex items-center justify-between p-3.5 {$borderRadius} {$backgroundClasses} {$borderClasses} transition-all duration-150";
        }

        // Enhanced modal items with smooth interactions for non-current items
        return "flex items-center justify-between p-3.5 {$borderRadius} hover:bg-{$this->primaryColor->value}-50 dark:hover:bg-{$this->primaryColor->value}-900/10 active:bg-{$this->primaryColor->value}-100 dark:active:bg-{$this->primaryColor->value}-800/20 transition-all duration-150";
    }

    public function getMobileModalItemContentClasses(): string
    {
        return "flex w-full items-center flex-1 min-h-[2.5rem]";
    }

    public function getMobileModalActionButtonClasses(): string
    {
        $borderRadius = match ($this->themeStyle) {
            ThemeStyle::Simple => 'rounded-full',
            ThemeStyle::Rounded => 'rounded-full',
            ThemeStyle::Square => 'rounded-md',
        };

        return "ms-3 p-2 text-{$this->secondaryColor->value}-400 hover:text-{$this->primaryColor->value}-600 dark:hover:text-{$this->primaryColor->value}-400 hover:bg-{$this->secondaryColor->value}-100 dark:hover:bg-{$this->secondaryColor->value}-800 {$borderRadius} transition-colors";
    }

    public function getMobileModalActionItemClasses(bool $isEnabled = true): string
    {
        // Theme-specific border radius with modern feel
        $borderRadius = match ($this->themeStyle) {
            ThemeStyle::Simple => 'rounded-lg',
            ThemeStyle::Rounded => 'rounded-xl',
            ThemeStyle::Square => 'rounded-md',  // Add subtle rounding for modern feel
        };

        // Compact action items with smaller text for breadcrumb context
        $baseClasses = "w-full flex items-center gap-2.5 px-3 py-2 min-h-[2rem] text-start text-sm {$borderRadius} transition-all duration-150 font-medium";

        if ($isEnabled) {
            $hoverClasses = "text-{$this->secondaryColor->value}-700 dark:text-{$this->secondaryColor->value}-200 hover:bg-{$this->primaryColor->value}-50 hover:text-{$this->primaryColor->value}-700 dark:hover:bg-{$this->primaryColor->value}-900/15 dark:hover:text-{$this->primaryColor->value}-300 active:bg-{$this->primaryColor->value}-100 dark:active:bg-{$this->primaryColor->value}-800/25";
            return "{$baseClasses} {$hoverClasses}";
        } else {
            return "{$baseClasses} opacity-50 cursor-not-allowed text-{$this->secondaryColor->value}-400 dark:text-{$this->secondaryColor->value}-500";
        }
    }

    public function getMobileModalCloseButtonClasses(): string
    {
        // Enhanced close button with modern hover states
        return "text-{$this->secondaryColor->value}-400 hover:text-{$this->primaryColor->value}-600 dark:hover:text-{$this->primaryColor->value}-300 hover:bg-{$this->secondaryColor->value}-100 dark:hover:bg-{$this->secondaryColor->value}-700/50 rounded-lg p-1 transition-all duration-150";
    }

    public function getMobileModalHeaderClasses(): string
    {
        return "text-base font-medium leading-6 text-{$this->secondaryColor->value}-900 dark:text-{$this->secondaryColor->value}-100";
    }

    public function getMobileModalIconClasses(): string
    {
        return "w-5 h-5 me-3 text-{$this->secondaryColor->value}-400 flex-shrink-0";
    }

    public function getMobileModalSeparatorClasses(): string
    {
        return "my-2 border-{$this->secondaryColor->value}-200 dark:border-{$this->secondaryColor->value}-600";
    }

    public function getMobileButtonClasses(): string
    {
        $borderRadius = match ($this->themeStyle) {
            ThemeStyle::Simple => 'rounded-lg',
            ThemeStyle::Rounded => 'rounded-xl',
            ThemeStyle::Square => 'rounded-md',
        };

        return "ms-3 {$borderRadius} p-2 text-{$this->secondaryColor->value}-500 transition-colors hover:bg-{$this->secondaryColor->value}-100 hover:text-{$this->primaryColor->value}-700 dark:text-{$this->secondaryColor->value}-400 dark:hover:bg-{$this->secondaryColor->value}-700 dark:hover:text-{$this->primaryColor->value}-200";
    }

    public function getCurrentStepMobileLabelClasses(): string
    {
        return "truncate font-medium flex-1 text-{$this->primaryColor->value}-900 dark:text-{$this->primaryColor->value}-100";
    }

    public function getMobileCurrentStepContainerClasses(): string
    {
        return "flex min-w-0 flex-1 items-center";
    }

    public function getDropdownSeparatorClasses(): string
    {
        return "my-1 border-{$this->secondaryColor->value}-200 dark:border-{$this->secondaryColor->value}-600";
    }

    public function getActionIconClasses(): string
    {
        return "h-4 w-4 text-{$this->secondaryColor->value}-400";
    }

    public function getSeparatorClasses(): string
    {
        return 'flex items-center justify-center px-1';
    }
}