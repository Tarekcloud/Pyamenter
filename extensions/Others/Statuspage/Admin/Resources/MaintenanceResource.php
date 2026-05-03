<?php

namespace Paymenter\Extensions\Others\Statuspage\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Paymenter\Extensions\Others\Statuspage\Models\Monitor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\MaintenanceResource\Pages\ListMaintenances;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\MaintenanceResource\Pages\CreateMaintenance;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\MaintenanceResource\Pages\EditMaintenance;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Paymenter\Extensions\Others\Statuspage\Models\Maintenance;
use Illuminate\Database\Eloquent\Model;

class MaintenanceResource extends Resource
{
    protected static ?string $model = Maintenance::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string | \UnitEnum | null $navigationGroup = 'Statuspage';

    protected static ?string $navigationLabel = 'Maintenance';

    public static function form(Schema $schema): Schema
    {
        $components = [
            TextInput::make('title')
                ->label('Title')
                ->required()
                ->maxLength(255),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            Textarea::make('description')
                ->label('Description')
                ->nullable(),

            Select::make('monitor_ids')
                ->label('Select Monitors')
                ->multiple()
                ->options(function () {
                    return Monitor::orderBy('name')->pluck('name', 'id');
                })
                ->searchable()
                ->helperText('Select monitors that will show in maintenance color during this maintenance')
                ->nullable(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'scheduled' => 'Scheduled',
                    'in_progress' => 'In Progress',
                    'investigating' => 'Investigating',
                    'monitoring' => 'Monitoring',
                    'completed' => 'Completed',
                ])
                ->required()
                ->default('scheduled')
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state === 'completed') {
                        $set('completed_at', now());
                    } elseif ($state === 'in_progress' && !request()->routeIs('*.edit')) {
                        $set('started_at', now());
                    }
                }),

            DateTimePicker::make('scheduled_at')
                ->label('Scheduled At')
                ->nullable(),

            DateTimePicker::make('started_at')
                ->label('Started At')
                ->nullable(),

            DateTimePicker::make('completed_at')
                ->label('Completed At')
                ->nullable(),
        ];

        if (request()->routeIs('*.edit')) {
            $components[] = Repeater::make('updates')
                ->label('Maintenance Updates')
                ->relationship('updates')
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'scheduled' => 'Scheduled',
                            'in_progress' => 'In Progress',
                            'investigating' => 'Investigating',
                            'monitoring' => 'Monitoring',
                            'completed' => 'Completed',
                        ])
                        ->required()
                        ->default('in_progress'),
                    Textarea::make('message')
                        ->label('Update Message')
                        ->required()
                        ->rows(3),
                ])
                ->defaultItems(0)
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['message'] ?? null)
                ->orderColumn('created_at');
        }

        return $schema->components($components);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'scheduled',
                        'primary' => 'in_progress',
                        'info' => 'investigating',
                        'success' => 'monitoring',
                        'gray' => 'completed',
                    ])
                    ->sortable(),
                TextColumn::make('started_at')->dateTime()->sortable(),
                TextColumn::make('scheduled_at')->dateTime()->sortable(),
                TextColumn::make('completed_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaintenances::route('/'),
            'create' => CreateMaintenance::route('/create'),
            'edit' => EditMaintenance::route('/{record}/edit'),
        ];
    }
}
