<?php

namespace Paymenter\Extensions\Others\Helpcenter\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Paymenter\Extensions\Others\Helpcenter\Models\Article;
use Paymenter\Extensions\Others\Helpcenter\Admin\Resources\KnowledgeBaseResource\Pages;

class KnowledgeBaseResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-m-academic-cap';
    protected static string | \UnitEnum | null $navigationGroup = 'Helpcenter';

    public static function form(Schema $schema): Schema
{
    return $schema->components([
        Select::make('category_id')
            ->label('Category')
            ->relationship('category', 'name')
            ->searchable()
            ->required()
            ->placeholder('Select a category'),

        TextInput::make('title')
            ->label('Title')
            ->required()
            ->maxLength(255)
            ->live(onBlur: true)
            ->afterStateUpdated(function ($get, $set, ?string $old, ?string $state) {
                if (($get('slug') ?? '') !== Str::slug($old)) {
                    return;
                }
                $set('slug', Str::slug($state));
            })
            ->placeholder('Fill in the name for your article'),

        TextInput::make('slug')
            ->label('Slug')
            ->required()
            ->maxLength(255)
            ->placeholder('Fill in the name for your article'),

        TextInput::make('description')
            ->label('Description')
            ->required()
            ->maxLength(255)
            ->placeholder('Small description'),

        DateTimePicker::make('published_at')
            ->label('Published at')
            ->placeholder('Date and time of publishment'),

        Toggle::make('is_active')
            ->label('Published')
            ->default(false),

        RichEditor::make('content')
            ->columnSpanFull()
            ->label('Content')
            ->placeholder('Enter the content of the article'),

        Section::make('Advanced')
            ->columns(1)
            ->collapsible()
            ->collapsed()
            ->columnSpanFull()
            ->schema([
                 Textarea::make('htmlcontent')
                      ->label('HTML Content')
                      ->placeholder('Paste or write HTML content here. This will overwrite ALL above settings, except visibility and navbar'),
                 Textarea::make('rawcontent')
                       ->label('Raw Content')
                       ->placeholder('Paste or write raw markdown content here.'),
                    ]),
           
    ]);


 }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category')->sortable(),
                TextColumn::make('published_at')->searchable()->dateTime()->sortable(),
                IconColumn::make('is_active')->label('Published')->boolean()->sortable(),
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
            'index' => Pages\ListKnowledgeBase::route('/'),
            'create' => Pages\CreateKnowledgeBase::route('/create'),
            'edit' => Pages\EditKnowledgeBase::route('/{record}/edit'),
        ];
    }
}
