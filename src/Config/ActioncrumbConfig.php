<?php

namespace Hdaklue\Actioncrumb\Config;

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
        
        return trim("{$baseClasses} {$themeClass} {$currentClass}");
    }

    public function getStepClasses(bool $isClickable, bool $isCurrent, bool $hasDropdown = false): string
    {
        $classes = ['inline-flex items-center'];
        
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
        $classes = [
            'inline-flex',
            'items-center',
            $padding,
            'cursor-pointer',
            'text-' . $this->secondaryColor->value . '-500',
            'hover:text-' . $this->primaryColor->value . '-600',
            'dark:text-' . $this->secondaryColor->value . '-400',
            'dark:hover:text-' . $this->primaryColor->value . '-400',
            'hover:bg-' . $this->secondaryColor->value . '-50',
            'dark:hover:bg-' . $this->secondaryColor->value . '-800/50',
            'rounded-md',
            'transition-colors',
            'duration-150'
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
        // Theme-specific border radius
        $borderRadius = match ($this->themeStyle) {
            ThemeStyle::Simple => 'rounded-md',    // Medium radius
            ThemeStyle::Rounded => 'rounded-xl',   // Extra large radius
            ThemeStyle::Square => 'rounded-none',  // No radius
        };
        
        // Use Tailwind classes with proper color configuration
        $classes = [
            'bg-white dark:bg-gray-800',
            'border border-' . $this->secondaryColor->value . '-200 dark:border-' . $this->secondaryColor->value . '-600',
            $borderRadius,
            'min-w-48 max-w-80 max-h-80',
            'overflow-y-auto',
            'shadow-lg',
            'py-1', // Small padding for proper item spacing
        ];
        
        return implode(' ', $classes);
    }

    public function getDropdownItemClasses(): string
    {
        // Use Tailwind classes with proper color configuration
        $classes = [
            'flex items-center gap-3',
            'w-full text-start',
            'px-4 py-2', // Better padding for full-width items
            'text-sm',
            'text-' . $this->secondaryColor->value . '-700 dark:text-' . $this->secondaryColor->value . '-300',
            'hover:bg-' . $this->secondaryColor->value . '-100 hover:text-' . $this->primaryColor->value . '-700',
            'dark:hover:bg-' . $this->secondaryColor->value . '-800 dark:hover:text-' . $this->primaryColor->value . '-300',
            'transition-colors duration-150',
            'cursor-pointer',
            'border-0 rounded-none', // No borders or radius
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
        return 'fixed inset-0 bg-black bg-opacity-50 transition-opacity';
    }

    public function getMobileModalContainerClasses(): string
    {
        $borderRadius = match ($this->themeStyle) {
            ThemeStyle::Simple => 'rounded-lg',
            ThemeStyle::Rounded => 'rounded-xl',
            ThemeStyle::Square => 'rounded-none',
        };

        return "relative transform overflow-hidden {$borderRadius} bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-start shadow-xl transition-all w-full max-w-full mx-4 sm:my-8 sm:w-full sm:max-w-sm sm:p-6 sm:mx-0";
    }

    public function getMobileModalItemClasses(): string
    {
        $borderRadius = match ($this->themeStyle) {
            ThemeStyle::Simple => 'rounded-lg',
            ThemeStyle::Rounded => 'rounded-xl',
            ThemeStyle::Square => 'rounded-none',
        };

        return "flex items-center justify-between p-3 {$borderRadius} hover:bg-{$this->secondaryColor->value}-50 dark:hover:bg-{$this->secondaryColor->value}-900/20 transition-colors";
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
        $borderRadius = match ($this->themeStyle) {
            ThemeStyle::Simple => 'rounded-lg',
            ThemeStyle::Rounded => 'rounded-xl',
            ThemeStyle::Square => 'rounded-none',
        };

        $baseClasses = "w-full flex items-center px-3 py-3 min-h-[2.5rem] text-start {$borderRadius} transition-colors";
        
        if ($isEnabled) {
            $hoverClasses = "text-{$this->secondaryColor->value}-700 dark:text-{$this->secondaryColor->value}-200 hover:bg-{$this->primaryColor->value}-50 hover:text-{$this->primaryColor->value}-700 dark:hover:bg-{$this->primaryColor->value}-900/10 dark:hover:text-{$this->primaryColor->value}-300";
            return "{$baseClasses} {$hoverClasses}";
        } else {
            return "{$baseClasses} opacity-50 cursor-not-allowed text-{$this->secondaryColor->value}-400 dark:text-{$this->secondaryColor->value}-500";
        }
    }

    public function getMobileModalCloseButtonClasses(): string
    {
        return "text-{$this->secondaryColor->value}-400 hover:text-{$this->primaryColor->value}-600 dark:hover:text-{$this->primaryColor->value}-300 transition-colors";
    }

    public function getMobileModalHeaderClasses(): string
    {
        return "text-lg font-medium leading-6 text-{$this->secondaryColor->value}-900 dark:text-{$this->secondaryColor->value}-100";
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

    /**
     * Classes for the separator wrapper between steps
     */
    public function getSeparatorClasses(): string
    {
        $margin = $this->compact ? 'mx-1' : 'mx-2';
        return trim("flex items-center {$margin}");
    }

    /**
     * HTML for the separator icon/element between steps
     */
    public function getSeparatorIcon(): string
    {
        // Delegate to the enum's SVG generator
        return $this->separatorType->getSvg();
    }
}