<?php

namespace Hdaklue\Actioncrumb;

use Hdaklue\Actioncrumb\Config\ActioncrumbConfig;
use Hdaklue\Actioncrumb\Enums\SeparatorType;
use Hdaklue\Actioncrumb\Enums\TailwindColor;
use Hdaklue\Actioncrumb\Enums\ThemeStyle;
use Illuminate\Support\ServiceProvider;

class ActioncrumbServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->setupConfig();
    }

    public function boot(): void
    {
        $this->loadViews();
        $this->publishAssets();
    }

    protected function setupConfig(): void
    {
        ActioncrumbConfig::make()
            ->themeStyle(ThemeStyle::Simple)
            ->separatorType(SeparatorType::Chevron)
            ->primaryColor(TailwindColor::Blue)
            ->secondaryColor(TailwindColor::Zinc)
            ->enableDropdowns(true)
            ->bind();
    }

    protected function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'actioncrumb');
    }

    protected function publishAssets(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/actioncrumb'),
            ], 'actioncrumb-views');
        }
    }
}
