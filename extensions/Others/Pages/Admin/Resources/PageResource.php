<?php

namespace Paymenter\Extensions\Others\Pages\Admin\Resources;

use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Paymenter\Extensions\Others\Pages\Admin\Resources\PageResource\Pages\ListPages;
use Paymenter\Extensions\Others\Pages\Admin\Resources\PageResource\Pages\CreatePage;
use Paymenter\Extensions\Others\Pages\Admin\Resources\PageResource\Pages\EditPage;
use Paymenter\Extensions\Others\Pages\Admin\Resources\PageResource\Pages;
use Paymenter\Extensions\Others\Pages\Admin\Resources\PageResource\RelationManagers;
use Paymenter\Extensions\Others\Pages\Models\Page;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\FileUpload;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-file-text-line';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')->rule('regex:/^[a-z0-9-]+$/')
                    ->required()->unique(ignoreRecord: true),
                Select::make('visibility')
                    ->options([
                        'public' => __('Public'),
                        'client' => __('Customers Only'),
                        'admin' => __('Admin Only'),
                    ])
                    ->default('public'),
                Select::make('navigation')
                    ->options([
                        'none' => __('None (hidden)'),
                        'top' => __('Top Navigation'),
                        'account_dropdown' => __('Account Dropdown (requires login)'),
                        'dashboard' => __('Client area/Dashboard (requires login)'),
                    ])
                    ->default('none')
                    ->label('Show in navigation'),
                Toggle::make('visible')
                    ->label('Visible'),
                Toggle::make('as_html')
                    ->live()
                    ->afterStateUpdated(fn(Toggle $component, Get $get) => $component
                        ->getContainer()
                        ->getComponent('editor_type')
                        ->getChildSchema()
                        ->fill())
                    ->label('Show content as raw HTML (also removes title from the page)'),
                Grid::make()
                    ->columnSpanFull()
                    ->key('editor_type')
                    ->schema(fn(Get $get): array => $get('as_html') ? [
                        CodeEditor::make('content')->language(Language::Html)->label('Content (HTML)')->columnSpanFull(),
                    ] : [
                        RichEditor::make('content')->label('Content (Markdown)')->columnSpanFull(),
                    ]),
                Section::make('Meta Information')
                    ->collapsed()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([

                        Textarea::make('description'),

                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('pages')
                            ->imageEditor(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('visible')
                    ->label('Visible')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
