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
        // For most themes, we let CSS handle the styling
        // Only return minimal classes needed for functionality
        $classes = ['inline-flex items-center'];
        
        // Add basic interaction states for compatibility
        if ($isClickable) {
            $classes[] = 'cursor-pointer';
        }
        
        return implode(' ', $classes);
    }

    public function getDropdownTriggerClasses(bool $isCurrent = false): string
    {
        // Basic dropdown trigger classes - styling handled by CSS
        $padding = $this->compact ? 'p-1' : 'p-1.5';
        $classes = ['inline-flex', 'items-center', $padding, 'cursor-pointer'];
        
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
        $secondaryColorClasses = $this->secondaryColor->getColorClasses();
        
        // Use Tailwind classes instead of custom CSS
        $classes = [
            'bg-white dark:bg-gray-800',
            'border border-' . $this->secondaryColor->value . '-500 dark:border-' . $this->secondaryColor->value . '-400',
            'rounded-none', // No border radius as requested
            'min-w-48 max-w-80 max-h-80',
            'overflow-y-auto',
            'shadow-lg',
            'p-0', // No padding for full-width items
        ];
        
        return implode(' ', $classes);
    }

    public function getDropdownItemClasses(): string
    {
        $secondaryColorClasses = $this->secondaryColor->getColorClasses();
        
        // Use Tailwind classes instead of custom CSS
        $classes = [
            'flex items-center gap-3',
            'w-full text-left',
            'px-6 py-3', // Proper padding for full-width items
            'text-sm',
            'text-gray-700 dark:text-gray-300',
            'hover:bg-' . $this->secondaryColor->value . '-50 dark:hover:bg-' . $this->secondaryColor->value . '-900/20',
            'transition-colors duration-150',
            'cursor-pointer',
            'border-0 rounded-none', // No borders or radius as requested
        ];
        
        return implode(' ', $classes);
    }
}