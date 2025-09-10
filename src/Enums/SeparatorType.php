<?php

namespace Hdaklue\Actioncrumb\Enums;

enum SeparatorType: string
{
    case Chevron = 'chevron';
    case Line = 'line';

    public function getSvg(): string
    {
        return match($this) {
            self::Chevron => '<x-icon name="heroicon-o-chevron-right" class="w-4 h-4 text-gray-400 flex-shrink-0 rtl:rotate-180" />',
            self::Line => '<div class="w-px h-4 bg-gray-300 dark:bg-gray-600 flex-shrink-0"></div>',
        };
    }
}