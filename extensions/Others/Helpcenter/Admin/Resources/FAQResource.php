<?php

namespace Paymenter\Extensions\Others\Helpcenter\Admin\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Paymenter\Extensions\Others\Helpcenter\Models\FAQ;
use Paymenter\Extensions\Others\Helpcenter\Admin\Resources\FAQResource\Pages;


class FAQResource extends Resource
{
    protected static ?string $model = FAQ::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-m-question-mark-circle';

    protected static string | \UnitEnum | null $navigationGroup = 'Helpcenter';
    
    protected static ?string $navigationLabel = 'FAQ';

    protected static ?string $pluralLabel = 'FAQs';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('article_id')
                    ->label('Linked Article')
                    ->relationship('article', 'title')
                    ->searchable()
                    ->nullable()
                    ->placeholder('No article (will show on KnowledgeBase home)'),

                TextInput::make('question')
                    ->label('Question')
                    ->required()
                    ->maxLength(255),

                RichEditor::make('answer')
                    ->label('Answer')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->label('Order')
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
                TextColumn::make('question')->searchable()->sortable(),
                TextColumn::make('article.title')->label('Article')->sortable(),
                TextColumn::make('sort_order')->sortable(),
                IconColumn::make('is_active')->label('Active')->boolean(),
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
        'index' => Pages\ListFAQs::route('/'),
        'create' => Pages\CreateFAQ::route('/create'),
        'edit' => Pages\EditFAQ::route('/{record}/edit'),
    ];
}

}
