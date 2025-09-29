{{-- WireStep component template - complete step rendering including container --}}
{{-- Note: WireStep components handle their own complete rendering structure --}}

<div class="flex flex-shrink-0 items-center">
    {{-- Step content with container classes --}}
    <div class="{{ app(\Hdaklue\Actioncrumb\Config\ActioncrumbConfig::class)->getStepContainerClasses(true) }}">
        <span class="{{ app(\Hdaklue\Actioncrumb\Config\ActioncrumbConfig::class)->getStepClasses(false, true) }}">
            <x-icon name="heroicon-o-user" class="me-2 h-5 w-5 flex-shrink-0" />
            {{ $user->name ?? 'User Details' }}
        </span>
    </div>

    {{-- Render step actions with dropdown arrow and mobile modal --}}
    {!! $this->renderStepActions() !!}
</div>

{{-- Include Filament Actions for component's own actions (separate from ActionCrumb actions) --}}
<x-filament-actions::modals />