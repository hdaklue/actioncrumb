# ActionCrumb

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hdaklue/actioncrumb.svg?style=flat-square)](https://packagist.org/packages/hdaklue/actioncrumb)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/hdaklue/actioncrumb/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/hdaklue/actioncrumb/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hdaklue/actioncrumb.svg?style=flat-square)](https://packagist.org/packages/hdaklue/actioncrumb)

**Smart breadcrumbs with contextual actions for Laravel applications.**

Transform cluttered admin toolbars into elegant, contextual navigation:
```
Dashboard > Users ⌄ > John Doe ⌄
              ↓         ↓
          [Export]   [Edit]
          [Import]   [Delete]
```

## Installation

```bash
composer require hdaklue/actioncrumb
```

**Requirements:** PHP 8.2+, Laravel 11+, Livewire 3+, Filament Actions 4+, Tailwind CSS, Alpine.js

## Basic Usage

### 1. Create a Breadcrumb Component

```php
<?php
namespace App\Livewire\Admin;

use Hdaklue\Actioncrumb\Components\WireCrumb;
use Hdaklue\Actioncrumb\{Step, Action};

class UsersManagement extends WireCrumb
{
    protected function actioncrumbs(): array
    {
        return [
            Step::make('Dashboard')
                ->icon('heroicon-o-home')
                ->url('/dashboard'),

            Step::make('Users')
                ->icon('heroicon-o-users')
                ->current()
                ->actions([
                    Action::make('Export')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->execute(fn() => $this->exportUsers()),

                    Action::make('Import')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->route('users.import'),
                ])
        ];
    }

    public function exportUsers()
    {
        // Your logic here
        $this->dispatch('notify', 'Users exported!');
    }
}
```

### 2. Add to Your Blade Template

```blade
<div>
    {!! $this->renderActioncrumbs() !!}

    <!-- Your page content -->
</div>
```

### 3. Include Required CSS/JS

Add to your layout:
```blade
<!-- Tailwind CSS (required) -->
@vite(['resources/css/app.css'])

<!-- Alpine.js (required for dropdowns) -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

## Key Features

### Steps with Actions
```php
Step::make('Users')
    ->icon('heroicon-o-users')
    ->route('users.index')
    ->actions([
        Action::make('Export')->execute(fn() => $this->export()),
        Action::make('Settings')->url('/settings'),
    ])
```

### Current Step
```php
Step::make('Current Page')->current(true)
```

### Dynamic Actions
```php
Step::make('User')
    ->actions([
        Action::make('Edit')
            ->visible(fn() => auth()->user()->can('edit-users'))
            ->execute(fn() => $this->editUser()),
    ])
```

### Action Types
```php
// Execute callback
Action::make('Delete')->execute(fn() => $this->delete())

// Navigate to URL
Action::make('Settings')->url('/admin/settings')

// Navigate to route
Action::make('Users')->route('users.index', ['type' => 'active'])

// Using WireAction with Filament actions (recommended)
$wireAction = WireAction::make('test')->livewire($this);
$action = $wireAction->execute('testAction'); // Triggers mountAction('testAction')
// Use $action in Step.actions() array
```

## Advanced Usage

### Two Component Types

**Breadcrumb Navigation Components** - Use `WireCrumb` base class:
```php
use Hdaklue\Actioncrumb\Components\WireCrumb;

class UserManagement extends WireCrumb
{
    protected function actioncrumbs(): array
    {
        return [
            Step::make('Dashboard')->url('/dashboard'),
            Step::make('Users')->current(),
        ];
    }
}
```

**Individual Components** - Use `HasActionCrumbs` trait:
```php
use Hdaklue\Actioncrumb\Traits\HasActionCrumbs;

class UserComponent extends Component
{
    use HasActionCrumbs;

    protected function actioncrumbs(): array
    {
        return [
            Action::make('Edit')->execute(fn() => $this->edit()),
            Action::make('Delete')->execute(fn() => $this->delete()),
        ];
    }
}
```

### Filament Actions Integration

**Using WireAction** for executing Filament Actions:

```php
use Filament\Actions\Action as FilamentAction;
use Hdaklue\Actioncrumb\Action;

class UserWireStep extends Component implements HasActions
{
    use HasActionCrumbs;
    use InteractsWithActions;

    public function editAction(): FilamentAction
    {
        return FilamentAction::make('edit')
            ->form([
                TextInput::make('name')->required(),
                TextInput::make('email')->email(),
            ])
            ->action(function (array $data) {
                $this->user->update($data);
            });
    }

    // Option 1: Direct Action objects with mountAction
    protected function actioncrumbs(): array
    {
        return [
            Action::make('Edit')
                ->icon('heroicon-o-pencil')
                ->execute(fn() => $this->mountAction('edit')),

            Action::make('Delete')
                ->icon('heroicon-o-trash')
                ->execute(fn() => $this->mountAction('delete')),
        ];
    }

    // Option 2: Using WireAction for Filament integration
    protected function actioncrumbsWithWireAction(): array
    {
        $wireAction = WireAction::make('user-actions')->livewire($this);

        return [
            $wireAction->execute('edit'),   // Calls mountAction('edit')
            $wireAction->execute('delete'), // Calls mountAction('delete')
        ];
    }
}
```

## Configuration

ActionCrumb provides a fluent configuration API to customize appearance and behavior globally.

### Global Configuration

Configure ActionCrumb in your `AppServiceProvider`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Hdaklue\Actioncrumb\Configuration\ActioncrumbConfig;
use Hdaklue\Actioncrumb\Enums\{ThemeStyle, SeparatorType, TailwindColor};

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ActioncrumbConfig::make()
            ->themeStyle(ThemeStyle::Rounded)        // Simple, Rounded, Square
            ->separatorType(SeparatorType::Chevron)  // Chevron, Line
            ->primaryColor(TailwindColor::Blue)      // Any Tailwind color
            ->secondaryColor(TailwindColor::Gray)    // Secondary accents
            ->enableDropdowns(true)                  // Enable/disable dropdowns
            ->compact(false)                         // Compact spacing
            ->compactMenuOnMobile(true)              // Mobile-specific behavior
            ->bind();
    }
}
```

### Theme Styles

**Simple** - Clean, minimal design:
```php
ActioncrumbConfig::make()->themeStyle(ThemeStyle::Simple)
```

**Rounded** - Modern pill design:
```php
ActioncrumbConfig::make()->themeStyle(ThemeStyle::Rounded)
```

**Square** - Bold geometric design:
```php
ActioncrumbConfig::make()->themeStyle(ThemeStyle::Square)
```

### Available Colors

All Tailwind colors are supported:
```php
// Primary colors (current step, active states)
->primaryColor(TailwindColor::Blue)
->primaryColor(TailwindColor::Purple)
->primaryColor(TailwindColor::Green)

// Secondary colors (other steps, borders)
->secondaryColor(TailwindColor::Gray)
->secondaryColor(TailwindColor::Slate)
->secondaryColor(TailwindColor::Zinc)
```

### Separator Types

Choose between different separator styles:
```php
// Chevron arrows (default)
->separatorType(SeparatorType::Chevron)

// Vertical lines
->separatorType(SeparatorType::Line)
```

### Mobile Configuration

Control mobile behavior:
```php
// Show full breadcrumb with horizontal scroll on mobile
->compactMenuOnMobile(false)

// Show compact menu with hamburger on mobile
->compactMenuOnMobile(true)
```

### Complete Configuration Example

```php
ActioncrumbConfig::make()
    ->themeStyle(ThemeStyle::Rounded)
    ->separatorType(SeparatorType::Chevron)
    ->primaryColor(TailwindColor::Purple)
    ->secondaryColor(TailwindColor::Slate)
    ->enableDropdowns(true)
    ->compact(false)
    ->compactMenuOnMobile(true)
    ->bind();
```

### Styling
Publish views for customization:
```bash
php artisan vendor:publish --tag=actioncrumb-views
```

### Tailwind Configuration
Add to your `tailwind.config.js`:
```js
module.exports = {
    content: [
        './vendor/hdaklue/actioncrumb/resources/views/**/*.blade.php',
    ],
    // ...
}
```

## Common Patterns

### User Management Breadcrumb

```php
class UserManagement extends WireCrumb
{
    protected function actioncrumbs(): array
    {
        return [
            Step::make('Dashboard')->url('/dashboard'),
            Step::make('Users')->route('users.index')->actions([
                Action::make('Create User')->route('users.create'),
                Action::make('Export')->execute(fn() => $this->export()),
            ]),
            Step::make($this->user->name)->current(true),
        ];
    }
}
```

### Permission-Based Actions in Step Components
```php
class UserStepComponent extends Component
{
    use HasActionCrumbs;

    protected function actioncrumbs(): array
    {
        return [
            Action::make('Edit')
                ->visible(fn() => auth()->user()->can('edit-users'))
                ->execute(fn() => $this->edit()),

            Action::make('Delete')
                ->visible(fn() => auth()->user()->can('delete-users'))
                ->execute(fn() => $this->delete()),
        ];
    }
}
```

## Troubleshooting

**Icons not showing?**
```bash
composer require blade-ui-kit/blade-heroicons
```

**Dropdowns not working?**
Make sure Alpine.js is loaded:
```html
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

**Styling issues?**
Ensure Tailwind processes the package views:
```js
// tailwind.config.js
content: [
    './vendor/hdaklue/actioncrumb/resources/views/**/*.blade.php',
]
```

## License

The MIT License (MIT). See [License File](LICENSE.md) for more information.



