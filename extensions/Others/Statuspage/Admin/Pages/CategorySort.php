<?php

namespace Paymenter\Extensions\Others\Statuspage\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Paymenter\Extensions\Others\Statuspage\Models\StatusPageSettings;
use Paymenter\Extensions\Others\Statuspage\Models\Monitor;
use Illuminate\Support\Facades\DB;

class CategorySort extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-folder';
    protected static string | \UnitEnum | null $navigationGroup = 'Statuspage';
    protected static ?string $navigationLabel = 'Categories';
    protected static ?string $title = 'Categories';
    protected string $view = 'statuspage::admin.category-sort';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = StatusPageSettings::getSettings();
        $categorySortOrder = $settings->category_sort_order ?? [];
        
        $categories = Monitor::distinct()->pluck('category')->filter()->map(function ($cat) {
            return $cat ?: 'Uncategorized';
        })->unique()->values();
        
        if (empty($categorySortOrder)) {
            $categorySortOrder = $categories->toArray();
        } else {
            $existingInOrder = array_intersect($categorySortOrder, $categories->toArray());
            $newCategories = $categories->diff($categorySortOrder)->toArray();
            $categorySortOrder = array_merge($existingInOrder, $newCategories);
        }
        
        $formData = [
            'categories' => collect($categorySortOrder)->map(function ($category) {
                return ['name' => $category];
            })->toArray(),
        ];
        
        $this->form->fill($formData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Categories')
                        ->description('Add, remove, and reorder categories. Categories will appear in this order on the status page.')
                        ->schema([
                            Repeater::make('categories')
                                ->label('Categories')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Category Name')
                                        ->required()
                                        ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, $get) {
                                            $allNames = collect($get('../../categories'))->pluck('name')->filter();
                                            $currentName = $get('name');
                                            $currentIndex = $get('../../categories');
                                            
                                            return $rule->where(function ($query) use ($allNames, $currentName) {
                                            });
                                        })
                                        ->helperText('Category name must be unique'),
                                ])
                                ->defaultItems(0)
                                ->reorderable()
                                ->addActionLabel('Add Category')
                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New Category')
                                ->collapsible(),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save Categories')
                                ->submit('save')
                                ->color('primary')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $categories = collect($data['categories'] ?? [])
            ->pluck('name')
            ->filter()
            ->map(function ($name) {
                return trim($name);
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
        
        if (count($categories) !== count(array_unique($categories))) {
            Notification::make()
                ->title('Error')
                ->body('Duplicate category names are not allowed')
                ->danger()
                ->send();
            return;
        }
        
        $settings = StatusPageSettings::getSettings();
        $settings->update(['category_sort_order' => $categories]);

        Notification::make()
            ->title('Categories saved successfully')
            ->success()
            ->send();
    }
}
