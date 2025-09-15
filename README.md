# ActionCrumb 🍞

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hdaklue/actioncrumb.svg?style=flat-square)](https://packagist.org/packages/hdaklue/actioncrumb)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/hdaklue/actioncrumb/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/hdaklue/actioncrumb/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hdaklue/actioncrumb.svg?style=flat-square)](https://packagist.org/packages/hdaklue/actioncrumb)

**Enhance Your Laravel Navigation** with intelligent breadcrumbs that do more than just show where you are. ActionCrumb transforms traditional breadcrumbs into powerful, interactive navigation components with contextual dropdown actions.

> **The Problem**: Standard breadcrumbs are static and force users into complex navigation patterns. Admin panels need quick actions, but adding toolbar buttons everywhere creates UI bloat and poor mobile experiences.

> **The Solution**: ActionCrumb gives you beautiful, responsive breadcrumbs where each step can have contextual dropdown actions. Export users from the Users breadcrumb. Delete records from the Show page. Import data without leaving your current view.

## 📖 Table of Contents

- [Why ActionCrumb?](#why-actioncrumb-)
- [Installation](#installation-)
- [Tailwind CSS Configuration](#tailwind-css-configuration-)
- [Quick Start](#quick-start-)
- [Filament Actions Integration](#filament-actions-integration-)
  - [WireAction - Execute Filament Actions](#wireaction---execute-filament-actions-from-breadcrumbs)
  - [WireCrumb - Dedicated Components](#wirecrumb---dedicated-filament-action-components)
  - [Bulk WireAction Creation](#bulk-wireaction-creation)
  - [Advanced Features](#advanced-features)
- [Configuration](#configuration-)
  - [Theme Styles](#theme-styles-)
  - [Available Colors](#available-colors-)
  - [RTL Support](#rtl-support-)
- [Advanced Usage](#advanced-usage-)
  - [Dynamic Labels and URLs](#dynamic-labels-and-urls-with-closures)
  - [Dynamic Actions Based on Permissions](#dynamic-actions-based-on-permissions)
  - [Conditional Steps](#conditional-steps)
  - [Complex Action Patterns](#complex-action-patterns)
- [API Reference](#api-reference-)
  - [Step Builder Methods](#step-builder-methods)
  - [Action Builder Methods](#action-builder-methods)
  - [WireAction Builder Methods](#wireaction-builder-methods-filament-integration)
  - [WireCrumb Abstract Component](#wirecrumb-abstract-component)
  - [Component Integration](#component-integration)
- [Real-World Examples](#real-world-examples-)
  - [Filament Admin Panel](#filament-admin-panel-with-modal-actions)
  - [E-commerce Admin](#e-commerce-admin-panel)
  - [CRM User Management](#crm-user-management)
  - [Content Management](#content-management)
- [Troubleshooting](#troubleshooting-)
  - [Common Issues](#common-issues)
  - [Performance Tips](#performance-tips)
- [Customization](#customization-)
  - [Publishing Views](#publishing-views)
  - [Custom CSS](#custom-css)
- [Contributing](#contributing-)
- [Security](#security-)
- [Credits](#credits-)
- [License](#license-)

## Why ActionCrumb? 🚀

**Transform This** ❌
```
Dashboard > Users > Profile
[Edit Button] [Delete Button] [Export Button] [Settings Button]
```

**Into This** ✅
```
Dashboard > Users ⌄ > Profile ⌄
              ↓       ↓
         [Export]  [Edit]
         [Import]  [Delete]  
         [Settings] [Share]
```

### Key Differentiators

- **🎯 Contextual Actions** - Actions belong to breadcrumb steps, not cluttered toolbars
- **📱 Mobile-First Design** - Clean dropdowns instead of overwhelming button rows
- **⚡ Zero JavaScript Bloat** - Uses Alpine.js components you already have
- **🎨 Tailwind 4 Native** - Built for modern Tailwind with full dark mode support
- **🔧 Config-Free** - Intelligent defaults with enum-based configuration (no config files!)
- **🎭 Theme Flexibility** - 3 built-in styles: Simple, Rounded (pills), Square (buttons)

## Installation 📦

**Requirements**
- PHP 8.2+
- Laravel 11.0+ / 12.0+
- Livewire 3.0+
- Filament Actions 4.0+ (for advanced action integration)
- Tailwind CSS 4.0+
- Alpine.js 3.0+

```bash
composer require hdaklue/actioncrumb
```

The package automatically registers via Laravel's service provider discovery. No config file publishing needed!

## Tailwind CSS Configuration 🎨

Since ActionCrumb uses dynamic color classes, add these `@source` directives to your main CSS file (usually `resources/css/app.css`):

```css
/* Scan ActionCrumb package files for classes */
@source "vendor/hdaklue/actioncrumb/src/Config/ActioncrumbConfig.php";
@source "vendor/hdaklue/actioncrumb/resources/views/components/*.blade.php";
@source "vendor/hdaklue/actioncrumb/resources/views/components/modals/*.blade.php";
@source "vendor/hdaklue/actioncrumb/src/Enums/*.php";
```

This ensures all dynamic color classes are included in your CSS build.

## Quick Start ⚡

**1. Add the Trait to Your Livewire Component**

```php
<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Hdaklue\Actioncrumb\Traits\HasActionCrumbs;
use Hdaklue\Actioncrumb\{Step, Action};

class UsersManagement extends Component
{
    use HasActionCrumbs;
    
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
                    Action::make('Export Users')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->execute(fn() => $this->exportUsers()),
                        
                    Action::make('Import Users')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->route('users.import'),
                        
                    Action::make('User Settings')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->url('/admin/users/settings')
                ])
        ];
    }
    
    public function exportUsers()
    {
        // Your export logic here
        $this->dispatch('notify', message: 'Users exported successfully!');
    }
    
    // ... rest of your component
}
```

**2. Render in Your Blade Template**


```blade
<div>
    <!-- Render your actioncrumbs -->
    {!! $this->renderActioncrumbs() !!}
    
    <!-- Your component content -->
    <div class="mt-6">
        {{-- Your users table, etc --}}
    </div>
</div>
```

**That's it!** Your breadcrumbs are now interactive with dropdown actions.

## Filament Actions Integration 🔥

ActionCrumb seamlessly integrates with **Filament Actions** for powerful modal-based workflows and form handling.

### WireAction - Execute Filament Actions from Breadcrumbs

The `WireAction` class allows you to execute Filament Actions directly from breadcrumb dropdowns:

```php
<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Hdaklue\Actioncrumb\Traits\HasActionCrumbs;
use Hdaklue\Actioncrumb\Support\WireAction;
use Hdaklue\Actioncrumb\{Step, Action};
use Filament\Actions\Action as FilamentAction;

class UsersManagement extends Component implements HasActions, HasSchemas
{
    use HasActionCrumbs;
    use InteractsWithActions;
    use InteractsWithSchemas;
    
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
                    // Execute Filament Actions through WireAction
                    WireAction::make('create-user')
                        ->label('Create User')
                        ->livewire($this)
                        ->icon('heroicon-o-plus')
                        ->execute('createUser'),
                        
                    WireAction::make('import-users')
                        ->label('Import Users')
                        ->livewire($this)
                        ->icon('heroicon-o-arrow-up-tray')
                        ->visible(fn() => auth()->user()->can('import-users'))
                        ->execute('importUsers'),
                        
                    WireAction::make('export-all')
                        ->label('Export All')
                        ->livewire($this)
                        ->icon('heroicon-o-arrow-down-tray')
                        ->execute('exportUsers'),
                ])
        ];
    }
    
    // Define your Filament Actions
    public function createUserAction(): FilamentAction
    {
        return FilamentAction::make('createUser')
            ->label('Create New User')
            ->icon('heroicon-o-plus')
            ->form([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'User',
                        'manager' => 'Manager',
                    ])
                    ->required(),
            ])
            ->action(function (array $data) {
                \App\Models\User::create($data);
                
                \Filament\Notifications\Notification::make()
                    ->title('User created successfully!')
                    ->success()
                    ->send();
            });
    }
    
    public function importUsersAction(): FilamentAction
    {
        return FilamentAction::make('importUsers')
            ->label('Import Users from CSV')
            ->icon('heroicon-o-arrow-up-tray')
            ->form([
                \Filament\Forms\Components\FileUpload::make('csv_file')
                    ->label('CSV File')
                    ->acceptedFileTypes(['text/csv', 'application/csv'])
                    ->required(),
            ])
            ->action(function (array $data) {
                // Handle CSV import logic
                $this->processUserImport($data['csv_file']);
            });
    }
    
    public function exportUsersAction(): FilamentAction
    {
        return FilamentAction::make('exportUsers')
            ->label('Export All Users')
            ->icon('heroicon-o-arrow-down-tray')
            ->action(function () {
                // Handle export logic
                return response()->download($this->generateUserExport());
            });
    }
    
    // ... rest of your component
}
```

### WireCrumb - Dedicated Filament Action Components

For complex workflows, extend the `WireCrumb` abstract component:

```php
<?php

namespace App\Livewire\Admin;

use Hdaklue\Actioncrumb\Components\WireCrumb;
use Hdaklue\Actioncrumb\Support\WireAction;
use Hdaklue\Actioncrumb\{Step};
use Filament\Actions\Action as FilamentAction;
use App\Models\User;

class UserProfileCrumb extends WireCrumb
{
    public ?User $user = null;
    
    public function mount($record = null, $parent = null)
    {
        parent::mount($record, $parent);
        $this->user = $record;
    }
    
    protected function actioncrumbs(): array
    {
        return [
            Step::make('Dashboard')->url('/dashboard'),
            Step::make('Users')->url('/admin/users'),
            Step::make($this->user->name ?? 'User')
                ->current()
                ->actions([
                    WireAction::make('edit-profile')
                        ->label('Edit Profile')
                        ->livewire($this)
                        ->icon('heroicon-o-pencil')
                        ->execute('editProfile'),
                        
                    WireAction::make('change-password')
                        ->label('Change Password')
                        ->livewire($this)
                        ->icon('heroicon-o-key')
                        ->execute('changePassword'),
                        
                    WireAction::make('suspend-user')
                        ->label('Suspend User')
                        ->livewire($this)
                        ->icon('heroicon-o-no-symbol')
                        ->visible(fn() => auth()->user()->can('suspend', $this->user))
                        ->execute('suspendUser'),
                ])
        ];
    }
    
    public function editProfileAction(): FilamentAction
    {
        return FilamentAction::make('editProfile')
            ->form([
                \Filament\Forms\Components\TextInput::make('name')
                    ->default($this->user->name)
                    ->required(),
                \Filament\Forms\Components\TextInput::make('email')
                    ->default($this->user->email)
                    ->email()
                    ->required(),
            ])
            ->action(function (array $data) {
                $this->user->update($data);
                
                \Filament\Notifications\Notification::make()
                    ->title('Profile updated successfully!')
                    ->success()
                    ->send();
            });
    }
    
    public function changePasswordAction(): FilamentAction
    {
        return FilamentAction::make('changePassword')
            ->form([
                \Filament\Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->minLength(8),
                \Filament\Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->same('password')
                    ->required(),
            ])
            ->action(function (array $data) {
                $this->user->update([
                    'password' => bcrypt($data['password'])
                ]);
                
                \Filament\Notifications\Notification::make()
                    ->title('Password changed successfully!')
                    ->success()
                    ->send();
            });
    }
    
    public function suspendUserAction(): FilamentAction
    {
        return FilamentAction::make('suspendUser')
            ->requiresConfirmation()
            ->modalHeading('Suspend User')
            ->modalDescription('Are you sure you want to suspend this user?')
            ->modalSubmitActionLabel('Yes, suspend')
            ->action(function () {
                $this->user->update(['suspended_at' => now()]);
                
                \Filament\Notifications\Notification::make()
                    ->title('User suspended successfully!')
                    ->success()
                    ->send();
                    
                return redirect('/admin/users');
            });
    }
}
```

Then render it in your Blade view:

```blade
<livewire:admin.user-profile-crumb :record="$user" />
```

### Bulk WireAction Creation

Create multiple actions efficiently:

```php
protected function actioncrumbs(): array
{
    $bulkActions = WireAction::bulk($this, [
        [
            'id' => 'create-document',
            'label' => 'Create Document',
            'action' => 'createDocument',
            'icon' => 'heroicon-o-document-plus',
        ],
        [
            'id' => 'import-documents',
            'label' => 'Import Documents',
            'action' => 'importDocuments',
            'icon' => 'heroicon-o-arrow-up-tray',
            'visible' => fn() => auth()->user()->can('import-documents'),
        ],
        [
            'id' => 'archive-all',
            'label' => 'Archive All',
            'action' => 'archiveAll',
            'icon' => 'heroicon-o-archive-box',
            'visible' => fn() => auth()->user()->can('archive-documents'),
        ],
    ]);
    
    return [
        Step::make('Documents')
            ->current()
            ->actions($bulkActions)
    ];
}
```

### Advanced Features

**Action Visibility:**
```php
WireAction::make('admin-action')
    ->label('Admin Only Action')
    ->livewire($this)
    ->visible(fn() => auth()->user()->isAdmin())
    ->execute('adminAction');

// Or static boolean
WireAction::make('delete-user')
    ->label('Delete User')
    ->livewire($this)
    ->visible(false) // Hide this action
    ->execute('deleteUser');
```

**Action Validation:**
```php
WireAction::make('delete-user')
    ->label('Delete User')
    ->livewire($this)
    ->validated(auth()->user()->can('delete', $this->user))
    ->execute('deleteUser');
```

**Action Parameters:**
```php
WireAction::make('send-email')
    ->label('Send Email')
    ->livewire($this)
    ->parameters(['template' => 'welcome'])
    ->execute('sendEmail');
```

**Debugging Actions:**
```php
// Get available actions on a component
$debug = WireAction::debug($this);
// Returns: ['class' => 'App\Livewire\UsersManagement', 'available_actions' => ['createUser', 'importUsers', ...]]
```

## Configuration 🎨

Configure ActionCrumb globally in your `AppServiceProvider` using our fluent configuration API:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Hdaklue\Actioncrumb\Config\ActioncrumbConfig;
use Hdaklue\Actioncrumb\Enums\{ThemeStyle, SeparatorType, TailwindColor, Direction};

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        ActioncrumbConfig::make()
            ->themeStyle(ThemeStyle::Rounded)        // Simple, Rounded, Square
            ->separatorType(SeparatorType::Chevron)  // Chevron, Line
            ->primaryColor(TailwindColor::Blue)      // Any Tailwind color
            ->secondaryColor(TailwindColor::Gray)    // Secondary accents
            ->direction(Direction::LTR)              // LTR, RTL
            ->darkMode(true)                         // Enable dark mode support
            ->bind();
    }
}
```

### Theme Styles 🎭

**Simple** (Clean, minimal)
```
Dashboard > Users ⌄ > Profile
```

**Rounded** (Modern pills)
```
(Dashboard) > (Users ⌄) > (Profile)
```

**Square** (Button-style with borders)
```
[Dashboard] > [Users ⌄] > [Profile]
```

### Available Colors 🌈

ActionCrumb supports all Tailwind colors: `Slate`, `Gray`, `Zinc`, `Neutral`, `Stone`, `Red`, `Orange`, `Amber`, `Yellow`, `Lime`, `Green`, `Emerald`, `Teal`, `Cyan`, `Sky`, `Blue`, `Indigo`, `Violet`, `Purple`, `Fuchsia`, `Pink`, `Rose`

### RTL Support 🌍

Full right-to-left layout support for international applications:

```php
ActioncrumbConfig::make()
    ->direction(Direction::RTL)
    ->bind();
```

## Advanced Usage 🔧

### Dynamic Labels and URLs with Closures

```php
protected function actioncrumbs(): array
{
    return [
        Step::make('dashboard')
            ->label('Dashboard')
            ->url('/dashboard'),
        
        Step::make('users')
            ->label('Users')
            ->route('users.index')
            ->actions([
                Action::make('export-users')
                    ->label(fn() => 'Export ' . $this->users->count() . ' Users')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->execute(fn() => $this->exportUsers()),
                    
                Action::make('view-profile')
                    ->label('View Profile')
                    ->url(fn() => route('users.show', $this->user->id))
                    ->visible(fn() => $this->user && auth()->user()->can('view', $this->user)),
            ]),
            
        Step::make('user-profile')
            ->label(fn() => $this->user->name ?? 'Unknown User')
            ->current()
            ->actions([
                Action::make('toggle-status')
                    ->label(fn() => $this->user->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn() => $this->user->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->execute(fn() => $this->toggleUserStatus()),
                    
                Action::make('send-email')
                    ->label('Send Email')
                    ->url(fn() => "mailto:{$this->user->email}")
                    ->enabled(fn() => !empty($this->user->email)),
            ])
    ];
}
```

### Dynamic Actions Based on Permissions

```php
protected function actioncrumbs(): array
{
    $userActions = collect([
        Action::make('View Profile')->route('users.show', $this->user->id)
    ]);
    
    if (auth()->user()->can('update', $this->user)) {
        $userActions->push(
            Action::make('Edit User')->route('users.edit', $this->user->id)
        );
    }
    
    if (auth()->user()->can('delete', $this->user)) {
        $userActions->push(
            Action::make('Delete User')
                ->icon('heroicon-o-trash')
                ->execute(fn() => $this->confirmDelete())
        );
    }
    
    return [
        Step::make('Dashboard')->url('/dashboard'),
        Step::make('Users')->route('users.index')->actions($userActions->toArray()),
        Step::make($this->user->name)->current()
    ];
}
```

### Conditional Steps

```php
protected function actioncrumbs(): array
{
    $steps = [
        Step::make('Dashboard')->url('/dashboard')
    ];
    
    if ($this->user->isAdmin()) {
        $steps[] = Step::make('Admin Panel')->url('/admin');
    }
    
    $steps[] = Step::make('Profile')->current();
    
    return $steps;
}
```

### Complex Action Patterns

```php
Action::make('Bulk Operations')
    ->execute(function() {
        return $this->dispatch('open-modal', modal: 'bulk-operations');
    }),

Action::make('Export')
    ->route('users.export', [
        'format' => 'xlsx',
        'filters' => $this->filters
    ]),

Action::make('Advanced Search')
    ->url('/users/search?' . http_build_query($this->searchParams))
```

## API Reference 📚

### Step Builder Methods

```php
Step::make('step-id')                           // Unique step ID (used as default label)
    ->label('Display Label')                    // Override label (string or closure)
    ->icon('heroicon-o-home')                   // Heroicon for the step
    ->url('/path')                              // Direct URL (string or closure)
    ->route('route.name', ['param' => 'value']) // Named route with parameters
    ->actions([Action::make('...')])            // Array of dropdown actions
    ->current(true)                             // Mark as current/active step
    ->visible(true)                             // Show/hide step (bool or closure)
    ->enabled(true)                             // Enable/disable step (bool or closure)

// Getter methods
->getId()                                       // Returns the unique step ID
->getLabel()                                    // Returns label or ID if no label set
```

### Action Builder Methods

```php
Action::make('action-id')                       // Unique action ID (used as default label)
    ->label('Display Label')                    // Override label (string or closure)
    ->icon('heroicon-o-download')               // Heroicon for the action
    ->url('/path')                              // Navigate to URL (string or closure)
    ->route('route.name', ['param' => 'value']) // Navigate to named route
    ->execute(fn() => $this->doSomething())     // Execute closure in component
    ->separator(true)                           // Add visual separator above
    ->visible(true)                             // Show/hide action (bool or closure)
    ->enabled(true)                             // Enable/disable action (bool or closure)

// Getter methods
->getId()                                       // Returns the unique action ID
->getLabel()                                    // Returns label or ID if no label set
```

### WireAction Builder Methods (Filament Integration)

```php
WireAction::make('action-id')                   // Unique action ID (required)
    ->label('Display Label')                    // Override label (string or closure)
    ->livewire($this)                           // Set Livewire component with HasActions
    ->icon('heroicon-o-star')                   // Heroicon for the action (optional)
    ->visible(true)                             // Show/hide action (bool or closure, default: true)
    ->execute('actionName')                     // Execute Filament Action method
    ->parameters(['key' => 'value'])            // Pass parameters to action
    ->validated(true)                           // Enable/disable action validation (default: true)

// Static methods
WireAction::bulk($component, $actions)          // Create multiple actions at once
WireAction::debug($component)                   // Debug available actions on component
```

### WireCrumb Abstract Component

```php
use Hdaklue\Actioncrumb\Components\WireCrumb;

class MyCustomCrumb extends WireCrumb
{
    // Required: Implement actioncrumbs method
    protected function actioncrumbs(): array { /* ... */ }
    
    // Optional: Override mount for custom initialization
    public function mount($record = null, $parent = null) { /* ... */ }
    
    // Optional: Override render for custom view
    public function render() { /* ... */ }
    
    // Define your Filament Actions
    public function myActionMethodAction(): \Filament\Actions\Action { /* ... */ }
}
```

### Component Integration

```php
use HasActionCrumbs;

// Required: Define your breadcrumbs
protected function actioncrumbs(): array { /* ... */ }

// Optional: Handle action events
protected function getListeners(): array
{
    return array_merge(parent::getListeners() ?? [], [
        'actioncrumb:action-executed' => 'handleActionResult'
    ]);
}

public function handleActionResult($event)
{
    // Handle the result of executed actions
    $this->dispatch('notify', message: "Action '{$event['action']}' completed!");
}
```

## Real-World Examples 🌟

### Filament Admin Panel with Modal Actions

```php
<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Hdaklue\Actioncrumb\Traits\HasActionCrumbs;
use Hdaklue\Actioncrumb\Support\WireAction;
use Hdaklue\Actioncrumb\{Step};
use Filament\Actions\Action as FilamentAction;
use App\Models\Product;

class ProductManagement extends Component implements HasActions, HasSchemas
{
    use HasActionCrumbs;
    use InteractsWithActions;
    use InteractsWithSchemas;
    
    public ?Product $product = null;
    
    protected function actioncrumbs(): array
    {
        return [
            Step::make('Dashboard')
                ->icon('heroicon-o-home')
                ->url('/admin'),
                
            Step::make('Products')
                ->icon('heroicon-o-cube')
                ->route('admin.products.index')
                ->actions([
                    WireAction::make('create-product')
                        ->label('Create Product')
                        ->livewire($this)
                        ->icon('heroicon-o-plus')
                        ->execute('createProduct'),
                        
                    WireAction::make('bulk-import')
                        ->label('Bulk Import')
                        ->livewire($this)
                        ->icon('heroicon-o-arrow-up-tray')
                        ->visible(fn() => auth()->user()->can('import-products'))
                        ->execute('bulkImport'),
                        
                    WireAction::make('export-catalog')
                        ->label('Export Catalog')
                        ->livewire($this)
                        ->icon('heroicon-o-arrow-down-tray')
                        ->execute('exportCatalog'),
                        
                    WireAction::make('manage-categories')
                        ->label('Manage Categories')
                        ->livewire($this)
                        ->icon('heroicon-o-tag')
                        ->execute('manageCategories'),
                ]),
                
            Step::make($this->product?->name ?? 'Product Details')
                ->current()
                ->actions([
                    WireAction::make('edit-details')
                        ->label('Edit Details')
                        ->livewire($this)
                        ->icon('heroicon-o-pencil')
                        ->execute('editProduct'),
                        
                    WireAction::make('update-inventory')
                        ->label('Update Inventory')
                        ->livewire($this)
                        ->icon('heroicon-o-cube')
                        ->execute('updateInventory'),
                        
                    WireAction::make('archive-product')
                        ->label('Archive Product')
                        ->livewire($this)
                        ->icon('heroicon-o-archive-box')
                        ->visible(fn() => $this->product && auth()->user()->can('archive', $this->product))
                        ->execute('archiveProduct'),
                ])
        ];
    }
    
    public function createProductAction(): FilamentAction
    {
        return FilamentAction::make('createProduct')
            ->label('Create New Product')
            ->icon('heroicon-o-plus')
            ->form([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\Textarea::make('description')
                    ->rows(3),
                \Filament\Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                \Filament\Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                \Filament\Forms\Components\FileUpload::make('images')
                    ->image()
                    ->multiple()
                    ->maxFiles(5),
            ])
            ->action(function (array $data) {
                $product = Product::create($data);
                
                \Filament\Notifications\Notification::make()
                    ->title('Product created successfully!')
                    ->success()
                    ->send();
                    
                return redirect()->route('admin.products.show', $product);
            });
    }
    
    public function editProductAction(): FilamentAction
    {
        return FilamentAction::make('editProduct')
            ->label('Edit Product Details')
            ->icon('heroicon-o-pencil')
            ->fillForm([
                'name' => $this->product->name,
                'description' => $this->product->description,
                'price' => $this->product->price,
                'category_id' => $this->product->category_id,
            ])
            ->form([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                \Filament\Forms\Components\Textarea::make('description')
                    ->rows(3),
                \Filament\Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                \Filament\Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
            ])
            ->action(function (array $data) {
                $this->product->update($data);
                
                \Filament\Notifications\Notification::make()
                    ->title('Product updated successfully!')
                    ->success()
                    ->send();
            });
    }
    
    public function updateInventoryAction(): FilamentAction
    {
        return FilamentAction::make('updateInventory')
            ->label('Update Inventory')
            ->icon('heroicon-o-cube')
            ->fillForm([
                'stock_quantity' => $this->product->stock_quantity,
                'low_stock_threshold' => $this->product->low_stock_threshold,
            ])
            ->form([
                \Filament\Forms\Components\TextInput::make('stock_quantity')
                    ->label('Stock Quantity')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                \Filament\Forms\Components\TextInput::make('low_stock_threshold')
                    ->label('Low Stock Alert Threshold')
                    ->numeric()
                    ->minValue(0),
            ])
            ->action(function (array $data) {
                $this->product->update($data);
                
                \Filament\Notifications\Notification::make()
                    ->title('Inventory updated successfully!')
                    ->success()
                    ->send();
            });
    }
    
    public function archiveProductAction(): FilamentAction
    {
        return FilamentAction::make('archiveProduct')
            ->label('Archive Product')
            ->icon('heroicon-o-archive-box')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Archive Product')
            ->modalDescription('Are you sure you want to archive this product? It will be hidden from customers but remain in your records.')
            ->modalSubmitActionLabel('Yes, Archive')
            ->action(function () {
                $this->product->update(['archived_at' => now()]);
                
                \Filament\Notifications\Notification::make()
                    ->title('Product archived successfully!')
                    ->success()
                    ->send();
                    
                return redirect()->route('admin.products.index');
            });
    }
}
```

### E-commerce Admin Panel

```php
protected function actioncrumbs(): array
{
    return [
        Step::make('Dashboard')
            ->icon('heroicon-o-home')
            ->url('/admin'),
            
        Step::make('Products')
            ->icon('heroicon-o-cube')
            ->route('admin.products.index')
            ->actions([
                Action::make('Import Products')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->route('admin.products.import'),
                Action::make('Export Catalog')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->execute(fn() => $this->exportCatalog()),
                Action::make('Bulk Edit Prices')
                    ->icon('heroicon-o-currency-dollar')
                    ->execute(fn() => $this->openBulkPriceModal())
            ]),
            
        Step::make($this->product->name)
            ->current()
            ->actions([
                Action::make('Duplicate Product')
                    ->icon('heroicon-o-document-duplicate')
                    ->execute(fn() => $this->duplicateProduct()),
                Action::make('View on Store')
                    ->icon('heroicon-o-eye')
                    ->url($this->product->public_url),
                Action::make('Delete Product')
                    ->icon('heroicon-o-trash')
                    ->execute(fn() => $this->confirmDelete())
            ])
    ];
}
```

### CRM User Management

```php
protected function actioncrumbs(): array
{
    return [
        Step::make('CRM')
            ->icon('heroicon-o-building-office-2')
            ->url('/crm'),
            
        Step::make('Contacts')
            ->icon('heroicon-o-users')
            ->route('crm.contacts.index')
            ->actions([
                Action::make('Import from CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->route('crm.contacts.import'),
                Action::make('Export All')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->execute(fn() => $this->exportContacts('all')),
                Action::make('Email Campaign')
                    ->icon('heroicon-o-envelope')
                    ->route('crm.campaigns.create')
            ]),
            
        Step::make($this->contact->full_name)
            ->current()
            ->actions([
                Action::make('Send Email')
                    ->icon('heroicon-o-envelope')
                    ->execute(fn() => $this->composeEmail()),
                Action::make('Schedule Call')
                    ->icon('heroicon-o-phone')
                    ->execute(fn() => $this->scheduleCall()),
                Action::make('Add to List')
                    ->icon('heroicon-o-plus')
                    ->execute(fn() => $this->showListModal())
            ])
    ];
}
```

### Content Management

```php
protected function actioncrumbs(): array
{
    return [
        Step::make('Content')
            ->icon('heroicon-o-document-text')
            ->url('/admin/content'),
            
        Step::make('Blog Posts')
            ->icon('heroicon-o-newspaper')
            ->route('admin.posts.index')
            ->actions([
                Action::make('New Post')
                    ->icon('heroicon-o-plus')
                    ->route('admin.posts.create'),
                Action::make('Categories')
                    ->icon('heroicon-o-tag')
                    ->route('admin.categories.index'),
                Action::make('Publish Queue')
                    ->icon('heroicon-o-clock')
                    ->route('admin.posts.scheduled')
            ]),
            
        Step::make($this->post->title)
            ->current()
            ->actions([
                Action::make('Preview')
                    ->icon('heroicon-o-eye')
                    ->url($this->post->preview_url),
                Action::make('SEO Analysis')
                    ->icon('heroicon-o-magnifying-glass')
                    ->execute(fn() => $this->runSeoAnalysis()),
                Action::make('Social Share')
                    ->icon('heroicon-o-share')
                    ->execute(fn() => $this->openShareModal())
            ])
    ];
}
```

## Troubleshooting 🔧

### Common Issues

**Q: Actions not executing in dropdown**
```php
// ❌ Wrong: Missing event listener
class MyComponent extends Component 
{
    use HasActionCrumbs;
    // Missing getListeners() method
}

// ✅ Correct: Proper trait usage
class MyComponent extends Component 
{
    use HasActionCrumbs; // This automatically adds the required listeners
}
```

**Q: Icons not displaying**
```bash
# Make sure Heroicons are installed
composer require blade-ui-kit/blade-heroicons

# Publish the icons
php artisan vendor:publish --tag=blade-heroicons
```

**Q: Dropdowns not working**
```blade
{{-- ❌ Missing Alpine.js --}}
<div>
    {!! $this->renderActioncrumbs() !!}
</div>

{{-- ✅ Alpine.js included --}}
<div x-data>
    {!! $this->renderActioncrumbs() !!}
</div>

{{-- Or include Alpine.js globally --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

**Q: Styling not applied**
```bash
# Make sure your Tailwind build includes ActionCrumb classes
# Add to your tailwind.config.js:
module.exports = {
  content: [
    './vendor/hdaklue/actioncrumb/resources/**/*.blade.php',
    // ... your other paths
  ],
}

# Then rebuild
npm run build
```

### Performance Tips

**Optimize for Large Menus**
```php
// Cache expensive operations
protected function actioncrumbs(): array
{
    return once(function() {
        return [
            // Your breadcrumbs...
        ];
    });
}

// Or use caching for user permissions
protected function actioncrumbs(): array
{
    $userPermissions = Cache::remember(
        "user.{$this->user->id}.permissions", 
        60, 
        fn() => $this->user->getAllPermissions()
    );
    
    // Build actions based on cached permissions...
}
```

## Customization 🎨

### Publishing Views

Publish and customize the Blade components:

```bash
php artisan vendor:publish --tag=actioncrumb-views
```

This publishes views to `resources/views/vendor/actioncrumb/`:
- `components/actioncrumb.blade.php` - Main component
- `components/step.blade.php` - Individual step
- `components/action.blade.php` - Dropdown actions

### Custom CSS

Override default styling by targeting ActionCrumb classes:

```css
/* Custom dropdown styling */
.actioncrumb-dropdown {
    @apply shadow-xl border-2 border-purple-200;
}

.actioncrumb-dropdown-item {
    @apply px-6 py-3 text-purple-700 hover:bg-purple-50;
}

/* Custom step styling */
.actioncrumb-step.current {
    @apply bg-gradient-to-r from-purple-500 to-pink-500 text-white;
}
```

## Contributing 🤝

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Setup

```bash
# Clone the repository
git clone https://github.com/hdaklue/actioncrumb.git

# Install dependencies
composer install
npm install

# Run tests
composer test

# Run static analysis
composer analyse
```

## Security 🔒

If you discover any security vulnerabilities, please email [hassan@daklue.com](mailto:hassan@daklue.com) instead of using the issue tracker.

## Credits 🙏

ActionCrumb is built on the shoulders of giants:

- [Laravel](https://laravel.com) - The PHP framework for web artisans
- [Livewire](https://laravel-livewire.com) - Full-stack framework for Laravel
- [Alpine.js](https://alpinejs.dev) - Rugged, minimal framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Heroicons](https://heroicons.com) - Beautiful hand-crafted SVG icons

Special thanks to the Laravel community for inspiration and feedback.

## License 📄

ActionCrumb is open-source software licensed under the [MIT license](LICENSE.md).

---

**Built with ❤️ by [Hassan Ibrahim](https://github.com/hdaklue)**

---

### Why Choose ActionCrumb? 

**For Laravel Teams Building Admin Panels:**
- ✅ **30% Less UI Code** - Replace button toolbars with contextual actions
- ✅ **Better Mobile UX** - Responsive dropdowns vs. cramped button rows  
- ✅ **Consistent Navigation** - Unified breadcrumb + action pattern across your app
- ✅ **Developer Experience** - Fluent API that feels like native Laravel
- ✅ **Filament Integration** - Seamless WireAction support for modal forms and workflows

**For Agencies & Freelancers:**
- ✅ **Faster Client Delivery** - Pre-built interactive navigation components
- ✅ **Professional Polish** - Beautiful, modern breadcrumbs that impress clients
- ✅ **Easy Customization** - Match any brand with theme system
- ✅ **Maintainable Code** - Clean separation of navigation logic

Ready to transform your Laravel navigation? **[Get started in under 2 minutes](#quick-start-)**.
