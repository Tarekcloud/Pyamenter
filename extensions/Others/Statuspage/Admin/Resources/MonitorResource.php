<?php

namespace Paymenter\Extensions\Others\Statuspage\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Paymenter\Extensions\Others\Statuspage\Models\Monitor;
use Paymenter\Extensions\Others\Statuspage\Models\Notification;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\MonitorResource\Pages\ListMonitors;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\MonitorResource\Pages\CreateMonitor;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\MonitorResource\Pages\EditMonitor;

class MonitorResource extends Resource
{
    protected static ?string $model = Monitor::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bolt';
    protected static string | \UnitEnum | null $navigationGroup = 'Statuspage';
    protected static ?string $navigationLabel = 'Monitors';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),

                Textarea::make('description')
                    ->label('Description')
                    ->nullable(),

                TextInput::make('category')
                    ->label('Category')
                    ->required(),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first. Monitors are sorted by this value within their category.'),

                Select::make('type')
                    ->label('Monitor Type')
                    ->options([
                        'http' => 'HTTP',
                        'keyword' => 'Keyword',
                        'tcp' => 'TCP',
                        'ping' => 'Ping (ICMP)',
                        'dns' => 'DNS',
                        'ssl' => 'SSL Certificate',
                    ])
                    ->reactive()
                    ->required(),

                TextInput::make('url')
                    ->label('URL')
                    ->visible(fn ($get) => in_array($get('type'), ['http', 'keyword', 'ssl']))
                    ->url()
                    ->nullable(),

                TextInput::make('keyword')
                    ->label('Keyword')
                    ->visible(fn ($get) => $get('type') === 'keyword')
                    ->nullable(),

                TextInput::make('host')
                    ->label('Host / Domain')
                    ->visible(fn ($get) => in_array($get('type'), ['tcp', 'ping', 'dns', 'ssl']))
                    ->nullable()
                    ->helperText(fn ($get) => match($get('type')) {
                        'ping' => 'Hostname or IP address to ping',
                        'dns' => 'Domain name to check DNS for',
                        'ssl' => 'Domain name for SSL certificate check',
                        default => 'Hostname or IP address',
                    }),

                TextInput::make('port')
                    ->label('Port')
                    ->numeric()
                    ->visible(fn ($get) => $get('type') === 'tcp')
                    ->nullable(),

                TextInput::make('response')
                    ->label('Expected Response')
                    ->numeric()
                    ->default(200)
                    ->required()
                    ->visible(fn ($get) => $get('type') === 'http'),

                TextInput::make('timeout')
                    ->label('Timeout (sec)')
                    ->numeric()
                    ->default(10)
                    ->required(),

                Select::make('notifications')
                    ->label('Notification')
                    ->relationship('notifications', 'name')
                    ->multiple(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->label('Order')->sortable(),
                TextColumn::make('name')->label('Name')->sortable(),
                TextColumn::make('category')->label('Category')->sortable(),
                BadgeColumn::make('type')->colors([
                    'http' => 'primary',
                    'keyword' => 'success',
                    'tcp' => 'warning',
                ]),
                TextColumn::make('url')->limit(30),
                TextColumn::make('host')->limit(20),
                TextColumn::make('port'),
                BadgeColumn::make('last_status')
                    ->colors([
                        'up' => 'success',
                        'down' => 'danger',
                    ])
                    ->sortable(),
                TextColumn::make('last_checked_at')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([])
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
            'index' => ListMonitors::route('/'),
            'create' => CreateMonitor::route('/create'),
            'edit' => EditMonitor::route('/{record}/edit'),
        ];
    }
}
