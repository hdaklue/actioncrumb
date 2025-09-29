{{-- WireStep component template - renders step content without outer container --}}
{{-- Note: When used as WireStep, the actioncrumb template handles the container structure --}}

{{-- User step content --}}
<span class="{{ app(\Hdaklue\Actioncrumb\Config\ActioncrumbConfig::class)->getStepClasses(false, true) }}">
    <x-icon name="heroicon-o-user" class="me-2 h-5 w-5 flex-shrink-0" />
    {{ $user->name ?? 'User Details' }}
</span>

{{-- Render step actions using the new method --}}
{!! $this->renderStepActions() !!}

{{-- Include Filament Actions --}}
<x-filament-actions::modals />