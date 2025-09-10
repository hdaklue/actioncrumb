<?php

namespace Hdaklue\Actioncrumb\Enums;

enum Direction: string
{
    case LTR = 'ltr';
    case RTL = 'rtl';

    public function getDirectionClass(): string
    {
        return match($this) {
            self::LTR => 'ltr',
            self::RTL => 'rtl',
        };
    }

    public function getSpacingClass(): string
    {
        return match($this) {
            self::LTR => 'space-x-2',
            self::RTL => 'space-x-reverse space-x-2',
        };
    }
}