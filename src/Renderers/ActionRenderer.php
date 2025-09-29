<?php

namespace Hdaklue\Actioncrumb\Renderers;

use Hdaklue\Actioncrumb\Action;
use Hdaklue\Actioncrumb\Support\WireAction;

class ActionRenderer
{
    /**
     * Process mixed array of Action and WireAction objects
     *
     * @param array $actions Mixed array of Action and WireAction objects
     * @return array Array of Action objects only
     */
    public function processActionsForRendering(array $actions): array
    {
        $processedActions = [];

        foreach ($actions as $action) {
            if ($action === null) {
                // Skip null actions (from invisible WireActions)
                continue;
            } elseif ($action instanceof WireAction) {
                // WireAction objects need to be converted to Action objects
                // This should not happen if execute() was called, but handle gracefully
                throw new \InvalidArgumentException(
                    'WireAction objects cannot be used directly. ' .
                    'Call execute("methodName") to get an Action object. ' .
                    'Example: WireAction::make("test")->livewire($this)->execute("testAction")'
                );
            } elseif ($action instanceof Action) {
                // Regular Action objects pass through
                $processedActions[] = $action;
            } else {
                throw new \InvalidArgumentException(
                    'Actions must be instances of Action or WireAction. ' .
                    'Got: ' . (is_object($action) ? get_class($action) : gettype($action))
                );
            }
        }

        return $processedActions;
    }

    public static function make(): self
    {
        return new self();
    }
}