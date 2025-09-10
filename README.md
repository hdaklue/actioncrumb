# ActionCrumb 🍞

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hdaklue/actioncrumb.svg?style=flat-square)](https://packagist.org/packages/hdaklue/actioncrumb)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/hdaklue/actioncrumb/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/hdaklue/actioncrumb/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hdaklue/actioncrumb.svg?style=flat-square)](https://packagist.org/packages/hdaklue/actioncrumb)

**Enhance Your Laravel Navigation** with intelligent breadcrumbs that do more than just show where you are. ActionCrumb transforms traditional breadcrumbs into powerful, interactive navigation components with contextual dropdown actions.

> **The Problem**: Standard breadcrumbs are static and force users into complex navigation patterns. Admin panels need quick actions, but adding toolbar buttons everywhere creates UI bloat and poor mobile experiences.

> **The Solution**: ActionCrumb gives you beautiful, responsive breadcrumbs where each step can have contextual dropdown actions. Export users from the Users breadcrumb. Delete records from the Show page. Import data without leaving your current view.

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
- Tailwind CSS 4.0+
- Alpine.js 3.0+

```bash
composer require hdaklue/actioncrumb
```

The package automatically registers via Laravel's service provider discovery. No config file publishing needed!

## Quick Start ⚡

**1. Add the Trait to Your Livewire Component**

```php
<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Hdaklue\Actioncrumb\Traits\HasActionCrumbs;
use Hdaklue\Actioncrumb\ValueObjects\{Step, Action};

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
Step::make('Label')
    ->icon('heroicon-o-home')                   // Heroicon for the step
    ->url('/path')                              // Direct URL
    ->route('route.name', ['param' => 'value']) // Named route with parameters
    ->actions([Action::make('...')])            // Array of dropdown actions
    ->current(true)                             // Mark as current/active step
```

### Action Builder Methods

```php
Action::make('Label')
    ->icon('heroicon-o-download')               // Heroicon for the action
    ->url('/path')                              // Navigate to URL
    ->route('route.name', ['param' => 'value']) // Navigate to named route
    ->execute(fn() => $this->doSomething())     // Execute closure in component
    ->separator(true)                           // Add visual separator above
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

**For Agencies & Freelancers:**
- ✅ **Faster Client Delivery** - Pre-built interactive navigation components
- ✅ **Professional Polish** - Beautiful, modern breadcrumbs that impress clients
- ✅ **Easy Customization** - Match any brand with theme system
- ✅ **Maintainable Code** - Clean separation of navigation logic

Ready to transform your Laravel navigation? **[Get started in under 2 minutes](#quick-start-)**.
