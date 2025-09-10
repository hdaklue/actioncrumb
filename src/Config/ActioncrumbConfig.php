<?php

namespace Hdaklue\Actioncrumb\Config;

use Hdaklue\Actioncrumb\Enums\ThemeStyle;
use Hdaklue\Actioncrumb\Enums\SeparatorType;
use Hdaklue\Actioncrumb\Enums\TailwindColor;
use Hdaklue\Actioncrumb\Enums\Direction;

class ActioncrumbConfig
{
    protected ThemeStyle $themeStyle = ThemeStyle::Simple;
    protected SeparatorType $separatorType = SeparatorType::Chevron;
    protected TailwindColor $primaryColor = TailwindColor::Blue;
    protected TailwindColor $secondaryColor = TailwindColor::Gray;
    protected Direction $direction = Direction::LTR;
    protected bool $enableDropdowns = true;
    protected bool $darkMode = false;
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

    public function direction(Direction $direction): self
    {
        $this->direction = $direction;
        return $this;
    }

    public function enableDropdowns(bool $enable = true): self
    {
        $this->enableDropdowns = $enable;
        return $this;
    }

    public function darkMode(bool $dark = true): self
    {
        $this->darkMode = $dark;
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

    public function getDirection(): Direction
    {
        return $this->direction;
    }

    public function isDropdownsEnabled(): bool
    {
        return $this->enableDropdowns;
    }

    public function isDarkMode(): bool
    {
        return $this->darkMode;
    }

    public function getContainerClasses(): string
    {
        $baseClasses = 'flex items-center text-sm';
        $directionClass = $this->direction->getSpacingClass();
        $dirClass = $this->direction->getDirectionClass();
        
        return "{$baseClasses} {$directionClass} {$dirClass}";
    }

    public function getStepClasses(bool $isClickable, bool $isCurrent): string
    {
        $themeClasses = $this->themeStyle->getClasses()['step'];
        $primaryColors = $this->primaryColor->getColorClasses();
        $secondaryColors = $this->secondaryColor->getColorClasses();
        
        $classes = [$themeClasses];
        
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
        
        return "{$themeClasses} {$primaryColors['text']} {$primaryColors['hover_text']} {$primaryColors['hover_bg']} transition-colors duration-200 cursor-pointer";
    }

    public function getDropdownMenuClasses(): string
    {
        $bgClass = $this->isDarkMode() ? 'bg-gray-800 dark:bg-gray-800' : 'bg-white dark:bg-gray-800';
        $borderClass = 'border border-gray-200 dark:border-gray-700';
        $positionClass = $this->direction === Direction::RTL ? 'right-0' : 'left-0';
        
        return "absolute z-50 mt-1 {$bgClass} {$borderClass} rounded-md shadow-lg py-1 min-w-40 {$positionClass}";
    }

    public function getDropdownItemClasses(): string
    {
        $hoverBg = $this->isDarkMode() ? 'hover:bg-gray-700' : 'hover:bg-gray-50';
        $textColor = $this->isDarkMode() ? 'text-gray-200 hover:text-gray-100' : 'text-gray-700 hover:text-gray-900';
        $spacingClass = $this->direction === Direction::RTL ? 'space-x-reverse space-x-2' : 'space-x-2';
        
        return "flex items-center {$spacingClass} w-full px-3 py-2 text-sm {$textColor} {$hoverBg} text-left transition-colors duration-150 cursor-pointer";
    }
}