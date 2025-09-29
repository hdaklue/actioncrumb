{{-- Example: How to structure a WireStep component template --}}
{{-- This shows how FlowStep component should render itself when used with WireStep --}}

<div class="flex flex-shrink-0 items-center {{ app(\Hdaklue\Actioncrumb\Config\ActioncrumbConfig::class)->getStepContainerClasses(false) }}">
    {{-- Step label/content --}}
    <span class="{{ app(\Hdaklue\Actioncrumb\Config\ActioncrumbConfig::class)->getStepClasses(false, false) }}">
        @if($flow->icon ?? null)
            <x-icon name="{{ $flow->icon }}" class="me-2 h-5 w-5 flex-shrink-0" />
        @endif
        {{ $flow->name ?? 'Flow Step' }}
    </span>

    {{-- Render actions using the new renderStepActions method --}}
    {!! $this->renderStepActions() !!}
</div>