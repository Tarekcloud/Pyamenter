<?php

namespace Paymenter\Extensions\Others\Statuspage\Admin\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Paymenter\Extensions\Others\Statuspage\Models\StatusPageSettings as StatusPageSettingsModel;

class StatusPageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string | \UnitEnum | null $navigationGroup = 'Statuspage';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $title = 'Statuspage Settings';
    protected string $view = 'statuspage::admin.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = StatusPageSettingsModel::getSettings();
        $historyType = $settings->history_type ?? 'days';
        $historyDays = $settings->history_days ?? 30;
        
        $formData = [
            'history_type' => $historyType,
            'history_days' => $historyDays,
            'incidents_limit' => $settings->incidents_limit ?? 0,
            'maintenance_limit' => $settings->maintenance_limit ?? 0,
            'up_color' => $settings->up_color ?? '#16a34a',
            'down_color' => $settings->down_color ?? '#dc2626',
            'degraded_color' => $settings->degraded_color ?? '#f59e0b',
            'maintenance_color' => $settings->maintenance_color ?? '#3b82f6',
            'show_uptime_cards' => $settings->show_uptime_cards ?? true,
            'show_incidents' => $settings->show_incidents ?? true,
            'show_maintenance' => $settings->show_maintenance ?? true,
        ];
        
        if ($historyType === 'hours') {
            $formData['history_hours'] = round($historyDays * 24);
        } else {
            $formData['history_hours'] = 24; // default
        }
        
        $this->form->fill($formData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('History Settings')
                        ->description('Configure how much history to display')
                        ->schema([
                            Select::make('history_type')
                                ->label('History Type')
                                ->options([
                                    'days' => 'Days (e.g., 30 days)',
                                    'hours' => 'Hours (e.g., 24 hours)',
                                ])
                                ->required()
                                ->default('days')
                                ->reactive()
                                ->helperText('Choose whether to display history by days or hours'),

                            TextInput::make('history_days')
                                ->label('History Length')
                                ->numeric()
                                ->required()
                                ->default(30)
                                ->visible(fn ($get) => $get('history_type') === 'days')
                                ->helperText('Number of days of history to display (e.g., 30 for 30 days)'),

                            TextInput::make('history_hours')
                                ->label('History Length (Hours)')
                                ->numeric()
                                ->default(24)
                                ->visible(fn ($get) => $get('history_type') === 'hours')
                                ->helperText('Number of hours of history to display (e.g., 24 for 24 hours)')
                                ->afterStateUpdated(function (callable $set, $state) {
                                    if ($state) {
                                        $set('history_days', round($state / 24, 2));
                                    }
                                }),
                        ]),

                    Section::make('Display Limits')
                        ->description('Control how many items to display on the status page')
                        ->schema([
                            TextInput::make('incidents_limit')
                                ->label('How many incidents to display')
                                ->numeric()
                                ->default(0)
                                ->helperText('Enter 0 to display all incidents, or enter a number to limit (e.g., 5 for last 5 incidents)'),

                            TextInput::make('maintenance_limit')
                                ->label('How many maintenance items to display')
                                ->numeric()
                                ->default(0)
                                ->helperText('Enter 0 to display all maintenance items, or enter a number to limit (e.g., 5 for last 5 maintenance items)'),
                        ]),

                    Section::make('Appearance')
                        ->description('Customize colors and display options')
                        ->schema([
                            ColorPicker::make('up_color')
                                ->label('Up Status Color')
                                ->default('#16a34a')
                                ->helperText('Color for services that are up'),

                            ColorPicker::make('down_color')
                                ->label('Down Status Color')
                                ->default('#dc2626')
                                ->helperText('Color for services that are down'),

                            ColorPicker::make('degraded_color')
                                ->label('Degraded Status Color')
                                ->default('#f59e0b')
                                ->helperText('Color for services with degraded performance'),

                            ColorPicker::make('maintenance_color')
                                ->label('Maintenance Status Color')
                                ->default('#3b82f6')
                                ->helperText('Color for monitors during maintenance'),

                            Toggle::make('show_uptime_cards')
                                ->label('Show Uptime Cards')
                                ->default(true)
                                ->helperText('Display uptime percentage cards for each monitor'),

                            Toggle::make('show_incidents')
                                ->label('Show Incidents Section')
                                ->default(true)
                                ->helperText('Display incidents on the status page'),

                            Toggle::make('show_maintenance')
                                ->label('Show Maintenance Section')
                                ->default(true)
                                ->helperText('Display maintenance events on the status page'),
                        ])
                        ->collapsible(),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        if (isset($data['history_hours']) && $data['history_type'] === 'hours') {
            $data['history_days'] = (int)$data['history_hours'];
        }
        unset($data['history_hours']);

        $settings = StatusPageSettingsModel::getSettings();
        $settings->update($data);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }


}
