{{-- Example: How to structure a WireStep component template --}}
{{-- This shows how FlowStep component should render itself when used with WireStep --}}
{{-- Note: WireStep components handle their own complete rendering structure --}}

<div class="flex flex-shrink-0 items-center">
    {{-- Step content with container classes --}}
    <div class="{{ app(\Hdaklue\Actioncrumb\Config\ActioncrumbConfig::class)->getStepContainerClasses(false) }}">
        <span class="{{ app(\Hdaklue\Actioncrumb\Config\ActioncrumbConfig::class)->getStepClasses(false, false) }}">
            @if($flow->icon ?? null)
                <x-icon name="{{ $flow->icon }}" class="me-2 h-5 w-5 flex-shrink-0" />
            @endif
            {{ $flow->name ?? 'Flow Step' }}
        </span>
    </div>

    {{-- Render actions using the new renderStepActions method --}}
    {!! $this->renderStepActions() !!}
</div>