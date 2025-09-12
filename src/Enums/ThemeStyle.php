<?php

namespace Hdaklue\Actioncrumb\Enums;

enum ThemeStyle: string
{
    case Simple = 'simple';
    case Rounded = 'rounded';
    case Square = 'square';

    public function getClasses(): array
    {
        return match($this) {
            self::Simple => [
                'step' => 'inline-flex items-center',
                'dropdown_trigger' => 'inline-flex items-center',
                'container_base' => 'transition-colors duration-200 ease-out',
                'step_base' => 'transition-all duration-200 ease-out',
                'hover_effects' => 'hover:bg-slate-100 dark:hover:bg-slate-700',
                'active_effects' => 'active:bg-slate-200 dark:active:bg-slate-600',
                'focus_effects' => 'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50',
                'flat_accent' => 'border-l-4 border-transparent hover:border-blue-500',
            ],
            self::Rounded => [
                'step' => 'inline-flex items-center',
                'dropdown_trigger' => '',
                'container_base' => 'rounded-full border-2 transition-all duration-200 ease-out',
                'step_base' => 'transition-all duration-200 ease-out',
                'hover_effects' => 'hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20',
                'active_effects' => 'active:bg-blue-100 dark:active:bg-blue-800/30',
                'focus_effects' => 'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 focus:ring-offset-2',
                'flat_bg' => 'bg-white dark:bg-gray-800',
                'pill_indicator' => 'relative after:absolute after:inset-0 after:rounded-full after:border-2 after:border-transparent after:transition-colors after:duration-200',
            ],
            self::Square => [
                'step' => 'inline-flex items-center',
                'dropdown_trigger' => '',
                'container_base' => 'border-2 transition-all duration-150 ease-out',
                'step_base' => 'transition-all duration-150 ease-out',
                'hover_effects' => 'hover:border-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20',
                'active_effects' => 'active:bg-blue-100 dark:active:bg-blue-800/30 active:border-blue-700',
                'focus_effects' => 'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50',
                'flat_bg' => 'bg-white dark:bg-gray-800',
                'geometric_accent' => 'relative before:absolute before:top-0 before:left-0 before:w-2 before:h-2 before:bg-blue-500 before:opacity-0 before:transition-opacity before:duration-200',
                'corner_accent' => 'hover:before:opacity-100',
            ],
        };
    }

    public function getThemeDescription(): string
    {
        return match($this) {
            self::Simple => 'Ultra-clean flat design with subtle left border accent on hover',
            self::Rounded => 'Modern flat pill design with smooth rounded edges and color transitions',
            self::Square => 'Bold flat geometric design with sharp corners and corner accent indicators',
        };
    }
}