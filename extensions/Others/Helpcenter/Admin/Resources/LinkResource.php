<?php

namespace Paymenter\Extensions\Others\Helpcenter\Admin\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Paymenter\Extensions\Others\Helpcenter\Models\Link;
use Paymenter\Extensions\Others\Helpcenter\Admin\Resources\LinkResource\Pages; // ✅ import Pages

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-m-link';
    protected static string | \UnitEnum | null $navigationGroup = 'Helpcenter';

    protected static ?string $navigationLabel = 'Links';
    protected static ?string $pluralLabel = 'Links';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('url')
                    ->label('URL')
                    ->required()
                    ->maxLength(255),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('url')->sortable(),
                IconColumn::make('is_active')->boolean()->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLinks::route('/'),
            'create' => Pages\CreateLink::route('/create'),
            'edit' => Pages\EditLink::route('/{record}/edit'),
        ];
    }
}
