<?php

namespace Hdaklue\Actioncrumb\Providers;

use Hdaklue\Actioncrumb\Configuration\ActioncrumbConfig;
use Hdaklue\Actioncrumb\Enums\SeparatorType;
use Hdaklue\Actioncrumb\Enums\TailwindColor;
use Hdaklue\Actioncrumb\Enums\ThemeStyle;
use Hdaklue\Actioncrumb\Services\MobileDetector;
use Illuminate\Support\ServiceProvider;

class ActioncrumbServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->setupConfig();
        $this->registerServices();
    }

    public function boot(): void
    {
        $this->loadViews();
        $this->publishAssets();
    }

    protected function setupConfig(): void
    {
        ActioncrumbConfig::make()
            ->themeStyle(ThemeStyle::Rounded)
            ->separatorType(SeparatorType::Line)
            ->primaryColor(TailwindColor::Sky)
            ->secondaryColor(TailwindColor::Zinc)
            ->enableDropdowns(true)
            ->compactMenuOnMobile()
            ->compact()
            ->bind();
    }

    protected function registerServices(): void
    {
        $this->app->singleton(MobileDetector::class, function ($app) {
            return new MobileDetector();
        });
    }

    protected function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'actioncrumb');
    }

    protected function publishAssets(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../resources/views' => resource_path('views/vendor/actioncrumb'),
            ], 'actioncrumb-views');

            $this->publishes([
                __DIR__ . '/../../resources/css' => public_path('vendor/actioncrumb'),
            ], 'actioncrumb-assets');
        }
    }
}
