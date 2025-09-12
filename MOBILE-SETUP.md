# Mobile Enhancement Setup

The ActionCrumb package now includes mobile/tablet detection with enhanced mobile UX options. Here's how to set it up:

## Installation

1. **Install the dependency** (recommended for better device detection):
```bash
composer require jenssegers/agent
```

2. **Publish assets** (optional, for custom styling):
```bash
php artisan vendor:publish --tag=actioncrumb-assets
```

## Configuration

### Compact Menu on Mobile (Optional)
```php
// In your ServiceProvider or component
ActioncrumbConfig::make()
    ->compactMenuOnMobile(true)  // Show only current step + hamburger menu
    ->bind();
```

### Default Behavior (No config needed)
- Mobile/Tablet: Full breadcrumb with horizontal scroll + auto-scroll to end
- Desktop: Normal breadcrumb display

## Features

### Mobile Dropdown Actions
**Always enabled on mobile devices:**
- **Dropdown actions** automatically use modals instead of dropdowns
- **Touch-friendly** action buttons with proper sizing
- **Accessible** modal interface with backdrop

### Compact Menu Mode (`compactMenuOnMobile: true`)
When enabled, mobile and tablet users will see:
- **Current step display** with hamburger menu
- **Breadcrumb modal** showing all navigation steps  
- **Actions modal** for step-specific actions

### Default Mobile Behavior (`compactMenuOnMobile: false`)
- **Full breadcrumb** with horizontal scroll
- **Auto-scroll to end** to show current step
- **Modal actions** for better touch interaction
- **Touch-friendly** interface with momentum scrolling

## Fallback Support

The package works with or without `jenssegers/agent`:
- **With agent**: Advanced device detection
- **Without agent**: Basic user agent parsing fallback

## CSS Classes

Optional CSS classes for enhanced mobile experience:
```css
.scrollbar-hide {
    /* Hides scrollbar while maintaining functionality */
}

.actioncrumb-container {
    /* Container-specific mobile styles */
}
```

## Testing

Test mobile interface by:
1. Using browser dev tools mobile emulation
2. Accessing from actual mobile device
3. Setting `modalOnMobile(true)` in config