<?php

namespace Hdaklue\Actioncrumb\Enums;

enum TailwindColor: string
{
    case Blue = 'blue';
    case Red = 'red';
    case Green = 'green';
    case Yellow = 'yellow';
    case Purple = 'purple';
    case Pink = 'pink';
    case Indigo = 'indigo';
    case Gray = 'gray';
    case Slate = 'slate';
    case Zinc = 'zinc';
    case Neutral = 'neutral';
    case Stone = 'stone';
    case Orange = 'orange';
    case Amber = 'amber';
    case Lime = 'lime';
    case Emerald = 'emerald';
    case Teal = 'teal';
    case Cyan = 'cyan';
    case Sky = 'sky';
    case Violet = 'violet';
    case Fuchsia = 'fuchsia';
    case Rose = 'rose';

    public function getColorClasses(string $intensity = '600'): array
    {
        $color = $this->value;
        
        return [
            'text' => "text-{$color}-{$intensity} dark:text-{$color}-400",
            'hover_text' => "hover:text-{$color}-700 dark:hover:text-{$color}-300",
            'bg' => "bg-{$color}-{$intensity} dark:bg-{$color}-700",
            'hover_bg' => "hover:bg-{$color}-50 dark:hover:bg-{$color}-900/20",
            'border' => "border-{$color}-200 dark:border-{$color}-700",
        ];
    }
}