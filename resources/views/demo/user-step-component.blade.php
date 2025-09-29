{{-- Updated UserStepComponent template using new renderStepActions() pattern --}}
<div class="flex flex-shrink-0 items-center {{ app(\Hdaklue\Actioncrumb\Config\ActioncrumbConfig::class)->getStepContainerClasses(false) }}">
    {{-- User step content --}}
    <span class="{{ app(\Hdaklue\Actioncrumb\Config\ActioncrumbConfig::class)->getStepClasses(false, true) }}">
        <x-icon name="heroicon-o-user" class="me-2 h-5 w-5 flex-shrink-0" />
        {{ $user->name ?? 'User Details' }}
    </span>

    {{-- Render step actions using the new method --}}
    {!! $this->renderStepActions() !!}
</div>

{{-- Include Filament Actions --}}
<x-filament-actions::modals />