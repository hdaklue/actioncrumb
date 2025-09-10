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
                'step' => 'inline-flex items-center px-2 py-1',
                'dropdown_trigger' => 'inline-flex items-center px-2 py-1 hover:bg-opacity-10'
            ],
            self::Rounded => [
                'step' => 'inline-flex items-center px-3 py-1.5 rounded-full',
                'dropdown_trigger' => 'inline-flex items-center px-3 py-1.5 rounded-full hover:bg-opacity-10'
            ],
            self::Square => [
                'step' => 'inline-flex items-center px-3 py-2 border',
                'dropdown_trigger' => 'inline-flex items-center px-3 py-2 border hover:bg-opacity-10'
            ],
        };
    }
}