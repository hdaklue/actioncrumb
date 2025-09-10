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

    public function getContainerClasses(): string
    {
        $baseClasses = 'flex items-center text-sm';
        $spacingClass = $this->compact ? 'space-x-1 rtl:space-x-reverse' : 'space-x-2 rtl:space-x-reverse';
        
        return "{$baseClasses} {$spacingClass}";
    }

    public function getStepClasses(bool $isClickable, bool $isCurrent): string
    {
        $themeClasses = $this->themeStyle->getClasses()['step'];
        $primaryColors = $this->primaryColor->getColorClasses();
        $secondaryColors = $this->secondaryColor->getColorClasses();
        
        $classes = [$themeClasses];
        
        // Add compact spacing
        if ($this->compact) {
            $classes[] = 'px-1 py-0.5';
        }
        
        if ($isCurrent) {
            $classes[] = 'font-medium text-gray-900 dark:text-gray-100';
            if ($this->themeStyle !== ThemeStyle::Simple) {
                $classes[] = $secondaryColors['border'];
            }
        } elseif ($isClickable) {
            $classes[] = $primaryColors['text'] . ' ' . $primaryColors['hover_text'];
            $classes[] = 'transition-colors duration-200 cursor-pointer';
        } else {
            $classes[] = 'text-gray-500 dark:text-gray-400';
        }
        
        return implode(' ', $classes);
    }

    public function getDropdownTriggerClasses(): string
    {
        $themeClasses = $this->themeStyle->getClasses()['dropdown_trigger'];
        $primaryColors = $this->primaryColor->getColorClasses();
        $padding = $this->compact ? 'p-0.5' : 'p-1';
        
        return "{$themeClasses} {$padding} {$primaryColors['text']} {$primaryColors['hover_text']} {$primaryColors['hover_bg']} transition-colors duration-200 cursor-pointer";
    }

    public function getDropdownMenuClasses(): string
    {
        return 'absolute z-50 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg py-1 min-w-40 left-0 rtl:left-auto rtl:right-0';
    }

    public function getDropdownItemClasses(): string
    {
        $spacingClass = 'space-x-2 rtl:space-x-reverse';
        $padding = $this->compact ? 'px-2 py-1' : 'px-3 py-2';
        $textSize = $this->compact ? 'text-xs' : 'text-sm';
        
        return "flex items-center {$spacingClass} w-full {$padding} {$textSize} text-gray-700 hover:text-gray-900 dark:text-gray-200 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 text-left rtl:text-right transition-colors duration-150 cursor-pointer";
    }
}