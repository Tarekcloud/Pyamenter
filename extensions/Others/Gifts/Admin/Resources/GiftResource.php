<?php

namespace Paymenter\Extensions\Others\Gifts\Admin\Resources;

use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Paymenter\Extensions\Others\Gifts\Admin\Resources\GiftResource\Pages\CreateGift;
use Paymenter\Extensions\Others\Gifts\Admin\Resources\GiftResource\Pages\EditGift;
use Paymenter\Extensions\Others\Gifts\Admin\Resources\GiftResource\Pages\ListGifts;
use Paymenter\Extensions\Others\Gifts\Models\Gift;

class GiftResource extends Resource
{
    protected static ?string $model = Gift::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-gift-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-gift-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'Engagement';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Code')
                    ->maxLength(255)
                    ->unique(static::getModel(), 'code', ignoreRecord: true)
                    ->placeholder('Enter the gift code (leave empty to auto-generate)')
                    ->helperText('Leave empty to auto-generate a random 12-character code.'),

                Select::make('type')
                    ->label('Type')
                    ->required()
                    ->live()
                    ->options([
                        'coupon' => 'Coupon Code',
                        'credit' => 'Account Credit',
                        'service' => 'Free Service',
                        'discount' => 'Discount',
                        'extension' => 'Subscription Extension',
                        'upgrade' => 'Product Upgrade',
                    ])
                    ->placeholder('Select the type of gift'),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->placeholder('Optional description for this gift code'),

                Select::make('coupon_id')
                    ->label('Coupon')
                    ->relationship('coupon', 'code')
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get) => $get('type') === 'coupon' && !$get('allow_coupon_selection'))
                    ->required(fn (Get $get) => $get('type') === 'coupon' && !$get('allow_coupon_selection')),

                CheckboxList::make('coupon_ids')
                    ->label('Available Coupons')
                    ->options(fn () => Coupon::all()->pluck('code', 'id'))
                    ->searchable()
                    ->visible(fn (Get $get) => $get('type') === 'coupon' && $get('allow_coupon_selection'))
                    ->required(fn (Get $get) => $get('type') === 'coupon' && $get('allow_coupon_selection'))
                    ->helperText('Select multiple coupons that users can choose from'),

                Toggle::make('allow_coupon_selection')
                    ->label('Allow User to Choose Coupon')
                    ->default(false)
                    ->live()
                    ->visible(fn (Get $get) => $get('type') === 'coupon')
                    ->helperText('Let users choose from multiple coupons'),

                TextInput::make('credit_amount')
                    ->label('Credit Amount')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->visible(fn (Get $get) => $get('type') === 'credit' && !$get('allow_credit_range') && !$get('is_random_credit'))
                    ->required(fn (Get $get) => $get('type') === 'credit' && !$get('allow_credit_range') && !$get('is_random_credit'))
                    ->placeholder('Enter the credit amount'),

                TextInput::make('credit_min_amount')
                    ->label('Minimum Credit Amount')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->visible(fn (Get $get) => $get('type') === 'credit' && ($get('allow_credit_range') || $get('is_random_credit')))
                    ->required(fn (Get $get) => $get('type') === 'credit' && ($get('allow_credit_range') || $get('is_random_credit'))),

                TextInput::make('credit_max_amount')
                    ->label('Maximum Credit Amount')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->visible(fn (Get $get) => $get('type') === 'credit' && ($get('allow_credit_range') || $get('is_random_credit')))
                    ->required(fn (Get $get) => $get('type') === 'credit' && ($get('allow_credit_range') || $get('is_random_credit'))),

                Toggle::make('allow_credit_range')
                    ->label('Allow Credit Range')
                    ->default(false)
                    ->live()
                    ->visible(fn (Get $get) => $get('type') === 'credit' && !$get('is_random_credit'))
                    ->helperText('Let users choose an amount within the range'),

                Toggle::make('is_random_credit')
                    ->label('Random Credit Amount')
                    ->default(false)
                    ->live()
                    ->visible(fn (Get $get) => $get('type') === 'credit' && !$get('allow_credit_range'))
                    ->helperText('Automatically assign a random credit amount between Min and Max'),

                Select::make('currency_code')
                    ->label('Currency')
                    ->options(fn () => Currency::all()->pluck('code', 'code'))
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get) => $get('type') === 'credit' && !$get('allow_currency_selection'))
                    ->required(fn (Get $get) => $get('type') === 'credit' && !$get('allow_currency_selection'))
                    ->default(fn () => config('settings.default_currency', 'USD')),

                CheckboxList::make('currency_codes')
                    ->label('Available Currencies')
                    ->options(fn () => Currency::all()->pluck('code', 'code'))
                    ->searchable()
                    ->visible(fn (Get $get) => $get('type') === 'credit' && $get('allow_currency_selection'))
                    ->required(fn (Get $get) => $get('type') === 'credit' && $get('allow_currency_selection'))
                    ->helperText('Select currencies that users can choose from'),

                Toggle::make('allow_currency_selection')
                    ->label('Allow User to Choose Currency')
                    ->default(false)
                    ->live()
                    ->visible(fn (Get $get) => $get('type') === 'credit')
                    ->helperText('Let users choose from multiple currencies'),

                Select::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->visible(fn (Get $get) => $get('type') === 'service')
                    ->required(fn (Get $get) => $get('type') === 'service' && !$get('allow_user_selection'))
                    ->helperText(fn (Get $get) => $get('allow_user_selection') ? 'Optional: Pre-select a product, or let user choose' : 'Select the product for this gift'),

                Select::make('plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'name', fn (Builder $query, Get $get) => 
                        $query->where('priceable_type', Product::class)
                              ->where('priceable_id', $get('product_id'))
                    )
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get) => $get('type') === 'service')
                    ->required(fn (Get $get) => $get('type') === 'service' && !$get('allow_user_selection'))
                    ->helperText(fn (Get $get) => $get('allow_user_selection') ? 'Optional: Pre-select a plan, or let user choose' : 'Select the plan for this gift'),

                TextInput::make('trial_period')
                    ->label('Trial Period')
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn (Get $get) => $get('type') === 'service')
                    ->placeholder('Leave empty for no trial period')
                    ->helperText('How long the service is active before payment is required'),

                Select::make('trial_unit')
                    ->label('Trial Unit')
                    ->options([
                        'day' => 'Days',
                        'week' => 'Weeks',
                        'month' => 'Months',
                        'year' => 'Years',
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'service' && $get('trial_period'))
                    ->required(fn (Get $get) => $get('type') === 'service' && $get('trial_period')),

                CheckboxList::make('service_product_ids')
                    ->label('Available Products')
                    ->options(fn () => Product::all()->pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn (Get $get) => $get('type') === 'service' && $get('allow_multiple_services'))
                    ->helperText('Select products users can choose from'),

                CheckboxList::make('service_plan_ids')
                    ->label('Available Plans')
                    ->options(fn () => Plan::where('priceable_type', Product::class)->pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn (Get $get) => $get('type') === 'service' && $get('allow_multiple_services'))
                    ->helperText('Select plans users can choose from'),

                Toggle::make('allow_multiple_services')
                    ->label('Allow Multiple Service Options')
                    ->default(false)
                    ->live()
                    ->visible(fn (Get $get) => $get('type') === 'service')
                    ->helperText('Let users choose from multiple product/plan combinations'),

                TextInput::make('extension_period')
                    ->label('Extension Period')
                    ->numeric()
                    ->minValue(1)
                    ->visible(fn (Get $get) => $get('type') === 'extension' && !$get('allow_extension_range'))
                    ->required(fn (Get $get) => $get('type') === 'extension' && !$get('allow_extension_range'))
                    ->placeholder('Enter the number of periods to extend'),

                TextInput::make('extension_min_period')
                    ->label('Minimum Extension Period')
                    ->numeric()
                    ->minValue(1)
                    ->visible(fn (Get $get) => $get('type') === 'extension' && $get('allow_extension_range'))
                    ->required(fn (Get $get) => $get('type') === 'extension' && $get('allow_extension_range')),

                TextInput::make('extension_max_period')
                    ->label('Maximum Extension Period')
                    ->numeric()
                    ->minValue(1)
                    ->visible(fn (Get $get) => $get('type') === 'extension' && $get('allow_extension_range'))
                    ->required(fn (Get $get) => $get('type') === 'extension' && $get('allow_extension_range')),

                Toggle::make('allow_extension_range')
                    ->label('Allow Extension Range')
                    ->default(false)
                    ->live()
                    ->visible(fn (Get $get) => $get('type') === 'extension')
                    ->helperText('Let users choose extension period within range'),

                Select::make('extension_unit')
                    ->label('Extension Unit')
                    ->options([
                        'day' => 'Days',
                        'week' => 'Weeks',
                        'month' => 'Months',
                        'year' => 'Years',
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'extension')
                    ->required(fn (Get $get) => $get('type') === 'extension')
                    ->default('month'),

                Select::make('upgrade_product_id')
                    ->label('Upgrade Product')
                    ->relationship('upgradeProduct', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->visible(fn (Get $get) => $get('type') === 'upgrade')
                    ->required(fn (Get $get) => $get('type') === 'upgrade')
                    ->helperText('The product to upgrade to'),

                Select::make('upgrade_plan_id')
                    ->label('Upgrade Plan (Optional)')
                    ->relationship('upgradePlan', 'name', fn (Builder $query, Get $get) => 
                        $query->where('priceable_type', Product::class)
                              ->where('priceable_id', $get('upgrade_product_id'))
                    )
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get) => $get('type') === 'upgrade')
                    ->helperText('Leave empty to automatically upgrade to the next higher plan, or specify a specific plan'),

                TextInput::make('discount_amount')
                    ->label('Discount Amount')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->visible(fn (Get $get) => $get('type') === 'discount' && !$get('allow_discount_range'))
                    ->required(fn (Get $get) => $get('type') === 'discount' && !$get('allow_discount_range'))
                    ->placeholder('Enter the discount amount'),

                TextInput::make('discount_min_amount')
                    ->label('Minimum Discount Amount')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->visible(fn (Get $get) => $get('type') === 'discount' && $get('allow_discount_range'))
                    ->required(fn (Get $get) => $get('type') === 'discount' && $get('allow_discount_range')),

                TextInput::make('discount_max_amount')
                    ->label('Maximum Discount Amount')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->visible(fn (Get $get) => $get('type') === 'discount' && $get('allow_discount_range'))
                    ->required(fn (Get $get) => $get('type') === 'discount' && $get('allow_discount_range')),

                Toggle::make('allow_discount_range')
                    ->label('Allow Discount Range')
                    ->default(false)
                    ->live()
                    ->visible(fn (Get $get) => $get('type') === 'discount')
                    ->helperText('Let users choose discount amount within range'),

                Select::make('discount_type')
                    ->label('Discount Type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'discount')
                    ->required(fn (Get $get) => $get('type') === 'discount')
                    ->default('percentage'),

                Select::make('discount_currency_code')
                    ->label('Currency')
                    ->options(fn () => Currency::all()->pluck('code', 'code'))
                    ->searchable()
                    ->preload()
                    ->visible(fn (Get $get) => $get('type') === 'discount' && $get('discount_type') === 'fixed')
                    ->required(fn (Get $get) => $get('type') === 'discount' && $get('discount_type') === 'fixed')
                    ->default(fn () => config('settings.default_currency', 'USD')),

                TextInput::make('discount_minimum_order')
                    ->label('Minimum Order Value')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->visible(fn (Get $get) => $get('type') === 'discount')
                    ->placeholder('Leave empty for no minimum')
                    ->helperText('Minimum order value required to use this discount'),

                TextInput::make('discount_maximum_discount')
                    ->label('Maximum Discount Cap')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->visible(fn (Get $get) => $get('type') === 'discount')
                    ->placeholder('Leave empty for no cap')
                    ->helperText('Maximum discount amount (useful for percentage discounts)'),

                Toggle::make('discount_applies_to_all')
                    ->label('Apply to All Products')
                    ->default(true)
                    ->live()
                    ->visible(fn (Get $get) => $get('type') === 'discount')
                    ->helperText('If disabled, select specific products or categories'),

                CheckboxList::make('discount_product_ids')
                    ->label('Applicable Products')
                    ->options(fn () => Product::all()->pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn (Get $get) => $get('type') === 'discount' && !$get('discount_applies_to_all'))
                    ->helperText('Select products this discount applies to'),

                CheckboxList::make('discount_category_ids')
                    ->label('Applicable Categories')
                    ->options(fn () => \App\Models\Category::all()->pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn (Get $get) => $get('type') === 'discount' && !$get('discount_applies_to_all'))
                    ->helperText('Select categories this discount applies to'),

                TextInput::make('max_uses')
                    ->label('Max Uses')
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Leave empty for unlimited uses')
                    ->helperText('Maximum total number of times this code can be redeemed'),

                TextInput::make('max_uses_per_user')
                    ->label('Max Uses Per User')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required()
                    ->placeholder('Maximum number of times a single user can redeem this code'),

                DatePicker::make('starts_at')
                    ->label('Starts At')
                    ->placeholder('Leave empty to start immediately'),

                DatePicker::make('expires_at')
                    ->label('Expires At')
                    ->placeholder('Leave empty for no expiration'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Inactive codes cannot be redeemed'),

                Toggle::make('allow_user_selection')
                    ->label('Allow User Selection')
                    ->default(false)
                    ->live()
                    ->visible(fn (Get $get) => in_array($get('type'), ['service', 'extension', 'upgrade']))
                    ->helperText('Allow users to choose which service/product when redeeming'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('share_url')
                    ->label('Share URL')
                    ->formatStateUsing(fn ($record) => route('gifts.redeem.direct', ['code' => $record->code]))
                    ->copyable()
                    ->url(fn ($record) => route('gifts.redeem.direct', ['code' => $record->code]))
                    ->openUrlInNewTab(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'coupon' => 'info',
                        'credit' => 'success',
                        'service' => 'warning',
                        'discount' => 'primary',
                        'extension' => 'success',
                        'upgrade' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'coupon' => 'Coupon',
                        'credit' => 'Credit',
                        'service' => 'Service',
                        'discount' => 'Discount',
                        'extension' => 'Extension',
                        'upgrade' => 'Upgrade',
                        default => $state,
                    }),
                TextColumn::make('description')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->description),
                TextColumn::make('trial_period')
                    ->label('Trial Period')
                    ->visible(fn ($record) => $record && $record->type === 'service')
                    ->formatStateUsing(fn ($record) => $record->trial_period && $record->trial_unit 
                        ? "{$record->trial_period} {$record->trial_unit}(s)"
                        : 'No trial'
                    ),
                TextColumn::make('used_count')
                    ->label('Used')
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->used_count . ($record->max_uses ? ' / ' . $record->max_uses : ' / ∞')),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive'),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
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
            'index' => ListGifts::route('/'),
            'create' => CreateGift::route('/create'),
            'edit' => EditGift::route('/{record}/edit'),
        ];
    }
}