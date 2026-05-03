<?php

namespace Paymenter\Extensions\Others\Statuspage\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Paymenter\Extensions\Others\Statuspage\Models\Notification;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\NotificationResource\Pages\ListNotifications;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\NotificationResource\Pages\CreateNotification;
use Paymenter\Extensions\Others\Statuspage\Admin\Resources\NotificationResource\Pages\EditNotification;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';
    protected static string | \UnitEnum | null $navigationGroup = 'Statuspage';
    protected static ?string $navigationLabel = 'Notifications';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),

                        Textarea::make('description')
                            ->label('Description')
                            ->nullable(),

                        TextInput::make('discord_webhook')
                            ->label('Discord Webhook URL')
                            ->url()
                            ->required()
                            ->helperText('The Discord webhook URL to send notifications to'),

                        TextInput::make('discord_tag')
                            ->label('Optional Tag (@here, @everyone)')
                            ->nullable()
                            ->helperText('Optional Discord mention tag'),
                    ]),

                Section::make('Discord Embed Customization')
                    ->description('Customize the Discord embed. Use placeholders: {monitor}, {status}, {category}, {url}, {host}, {port}, {error}, {uptime}, {checked_at}')
                    ->schema([
                        TextInput::make('embed_title')
                            ->label('Embed Title')
                            ->placeholder('{statusEmoji} {monitor} is {status}')
                            ->helperText('Use placeholders: {monitor}, {status}, {statusEmoji}')
                            ->nullable(),

                        Textarea::make('embed_description')
                            ->label('Embed Description')
                            ->placeholder('Monitor status changed')
                            ->rows(3)
                            ->helperText('Optional description for the embed')
                            ->nullable(),

                        Repeater::make('embed_fields')
                            ->label('Embed Fields')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Field Name')
                                    ->required()
                                    ->placeholder('Category'),
                                Textarea::make('value')
                                    ->label('Field Value')
                                    ->required()
                                    ->rows(2)
                                    ->placeholder('{category}')
                                    ->helperText('Use placeholders in the value'),
                                Toggle::make('inline')
                                    ->label('Inline')
                                    ->default(true)
                                    ->helperText('Display field inline with other fields'),
                            ])
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New Field')
                            ->helperText('Add custom fields to the embed. Use placeholders in field values.'),

                        ColorPicker::make('embed_color_up')
                            ->label('Color (Up Status)')
                            ->default('#00FF00')
                            ->helperText('Color when monitor is up'),

                        ColorPicker::make('embed_color_down')
                            ->label('Color (Down Status)')
                            ->default('#FF0000')
                            ->helperText('Color when monitor is down'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->sortable(),
                TextColumn::make('discord_webhook')->label('Webhook')->limit(50),
                TextColumn::make('discord_tag')->label('Tag')->limit(20),
            ])
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
            'index' => ListNotifications::route('/'),
            'create' => CreateNotification::route('/create'),
            'edit' => EditNotification::route('/{record}/edit'),
        ];
    }
}
