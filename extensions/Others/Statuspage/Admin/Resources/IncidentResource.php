<?php

namespace Paymenter\Extensions\Others\Statuspage\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\IncidentResource\Pages\ListIncidents;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\IncidentResource\Pages\CreateIncident;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\IncidentResource\Pages\EditIncident;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\IncidentResource\Pages;
use Paymenter\Extensions\Others\Statuspage\Models\Incident;
use Paymenter\Extensions\Others\Statuspage\Models\Monitor;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string | \UnitEnum | null $navigationGroup = 'Statuspage';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('monitor_id')
                    ->label('Monitor')
                    ->options(Monitor::all()->pluck('name', 'id'))
                    ->nullable(),

                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'investigating' => 'Investigating',
                        'monitoring' => 'Monitoring',
                        'resolved' => 'Resolved',
                    ])
                    ->required()
                    ->default('investigating')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state === 'resolved') {
                            $set('resolved_at', now());
                        } else {
                            $set('resolved_at', null);
                        }
                    }),

                DateTimePicker::make('started_at')
                    ->label('Started At')
                    ->required(),

                DateTimePicker::make('resolved_at')
                    ->label('Resolved At')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('monitor.name')->label('Monitor')->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'investigating',
                        'warning' => 'monitoring',
                        'success' => 'resolved',
                    ])
                    ->sortable(),
                TextColumn::make('started_at')->dateTime()->sortable(),
                TextColumn::make('resolved_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncidents::route('/'),
            'create' => CreateIncident::route('/create'),
            'edit' => EditIncident::route('/{record}/edit'),
        ];
    }
}
