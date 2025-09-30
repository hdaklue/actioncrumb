<?php

namespace Hdaklue\Actioncrumb\Enums;

enum SeparatorType: string
{
    case Chevron = 'chevron';
    case Line = 'line';
    case Backslash = 'backslash';

    public function getSvg(): string
    {
        return match($this) {
            self::Chevron => '<svg class="w-4 h-4 text-gray-400 dark:text-gray-600 flex-shrink-0 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>',
            self::Line => '<div class="w-px h-4 bg-gray-300 dark:bg-gray-600 flex-shrink-0"></div>',
            self::Backslash => '<span class="text-gray-400 dark:text-gray-600 text-lg font-light rtl:rotate-180 inline-block">/</span>',
        };
    }
}