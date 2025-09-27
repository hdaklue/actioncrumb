# WireStep Usage Examples

## Basic WireStep with Actions

```php
<?php

use Hdaklue\Actioncrumb\Components\WireStep;
use Hdaklue\Actioncrumb\Support\WireAction;
use Filament\Actions\Action;

class DocumentWireStep extends WireStep
{
    public ?Document $document = null;

    public function mount(
        string $stepId,
        string|\Closure|null $label = null,
        ?string $icon = null,
        string|\Closure|null $url = null,
        ?string $route = null,
        array $routeParams = [],
        bool $current = false,
        bool|\Closure $visible = true,
        bool|\Closure $enabled = true,
        ?HasActions $parent = null,
        array $stepData = []
    ): void {
        parent::mount($stepId, $label, $icon, $url, $route, $routeParams, $current, $visible, $enabled, $parent, $stepData);

        // Load document from step data
        $this->document = $stepData['document'] ?? null;
    }

    protected function actioncrumbs(): array
    {
        return [
            WireAction::make('edit')
                ->label('Edit Document')
                ->icon('heroicon-o-pencil')
                ->livewire($this)
                ->visible(fn() => $this->document && auth()->user()->can('update', $this->document))
                ->execute('editDocument'),

            WireAction::make('delete')
                ->label('Delete Document')
                ->icon('heroicon-o-trash')
                ->livewire($this)
                ->visible(fn() => $this->document && auth()->user()->can('delete', $this->document))
                ->execute('deleteDocument'),
        ];
    }

    public function editDocumentAction(): Action
    {
        return Action::make('editDocument')
            ->label('Edit Document')
            ->form([
                TextInput::make('title')->required(),
                Textarea::make('content'),
            ])
            ->fillForm([
                'title' => $this->document->title,
                'content' => $this->document->content,
            ])
            ->action(function (array $data) {
                $this->document->update($data);

                Notification::make()
                    ->title('Document updated successfully')
                    ->success()
                    ->send();

                $this->dispatch('step:refresh');
            });
    }

    public function deleteDocumentAction(): Action
    {
        return Action::make('deleteDocument')
            ->label('Delete Document')
            ->requiresConfirmation()
            ->action(function () {
                $this->document->delete();

                Notification::make()
                    ->title('Document deleted successfully')
                    ->success()
                    ->send();

                return redirect()->route('documents.index');
            });
    }
}
```

## Using WireStep in WireCrumb

```php
<?php

use Hdaklue\Actioncrumb\Components\WireCrumb;
use Hdaklue\Actioncrumb\Step;

class DocumentCrumb extends WireCrumb
{
    public ?Document $document = null;

    public function mount($record = null, $parent = null)
    {
        parent::mount($record, $parent);
        $this->document = $record;
    }

    protected function actioncrumbs(): array
    {
        return [
            // Regular Step
            Step::make('Documents')
                ->route('documents.index')
                ->icon('heroicon-o-document-text'),

            // WireStep with fluent API
            DocumentWireStep::make('document-details')
                ->label($this->document?->title ?? 'New Document')
                ->icon('heroicon-o-document')
                ->current(true)
                ->stepData(['document' => $this->document])
                ->parent($this),
        ];
    }
}
```

## Converting Regular Step to WireStep

```php
// Convert existing Step to WireStep when needed
$regularStep = Step::make('documents')
    ->label('Documents')
    ->route('documents.index');

// Convert to component for advanced features
$wireStep = $regularStep->asComponent($this);
```

## Key Features

1. **Independent Actions**: Each WireStep can define its own Filament Actions
2. **Schema Support**: Full access to Filament schemas and form components
3. **State Management**: Steps can maintain their own state and data
4. **Parent Communication**: Access to parent WireCrumb when needed
5. **Event Handling**: Steps can dispatch and listen to events
6. **Reusability**: Components can be reused across different actioncrumbs

## Events

- `step:refresh` - Refresh the step's actions and state
- `step:refreshed` - Fired after step refresh completes
- `actioncrumb:action-executed` - Fired when an action is executed (inherited from HasActionCrumbs)