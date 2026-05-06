<?php

namespace App\Admin\Resources;

use App\Admin\Resources\FailedJobResource\Pages\ListFailedJobs;
use App\Models\FailedJob;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-error-warning-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-error-warning-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'Other';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')->toggleable(isToggledHiddenByDefault: true),
                
                // 1. 显示 Service ID 并附带跳转链接
                TextColumn::make('service_id')
                    ->label('Service ID')
                    ->getStateUsing(function ($record) {
                        $payload = json_decode($record->payload, true);
                        if (isset($payload['data']['command'])) {
                            $command = $payload['data']['command'];
                            if (preg_match('/"App\\\\Models\\\\Service";s:2:"id";i:(\d+);/', $command, $matches)) {
                                return $matches[1];
                            }
                        }
                        return '-';
                    })
                    ->url(fn ($state) => $state !== '-' ? url("/admin/services/services/{$state}/edit") : null)
                    ->color('primary')
                    ->icon(fn ($state) => $state !== '-' ? 'heroicon-o-link' : null)
                    ->searchable(false)
                    ->sortable(false),
                    
                // 2. 显示关联的 Product Name (产品名称)
                TextColumn::make('product_name')
                    ->label('Product Name')
                    ->getStateUsing(function ($record) {
                        $payload = json_decode($record->payload, true);
                        if (isset($payload['data']['command'])) {
                            $command = $payload['data']['command'];
                            
                            // 先正则匹配出 Service ID
                            if (preg_match('/"App\\\\Models\\\\Service";s:2:"id";i:(\d+);/', $command, $matches)) {
                                $serviceId = $matches[1];
                                
                                // 去数据库中查找这个 Service，并获取关联的 Product 名称
                                // 使用 \App\Models\Service 引入模型
                                $service = \App\Models\Service::find($serviceId);
                                
                                // 如果查到了 Service 且关联了 Product，返回产品名，否则返回提示
                                return $service?->product?->name ?? 'Unknown / Deleted';
                            }
                        }
                        return '-';
                    })
                    ->searchable(false)
                    ->sortable(false),
                    
                    
                TextColumn::make('payload')->formatStateUsing(function ($state) {
                    $state = json_decode($state);

                    // List displayName
                    return $state->displayName;
                }),
                TextColumn::make('exception')->formatStateUsing(function ($state) {
                    return explode("\n", $state)[0];
                })->wrap()->tooltip(function ($state) {
                    return $state;
                })->limit(200),
                TextColumn::make('failed_at'),
            ])
            ->poll()
            ->recordActions([
                Action::make('retry')
                    ->label('Retry')
                    ->requiresConfirmation()
                    ->action(function (FailedJob $record): void {
                        Artisan::call("queue:retry {$record->uuid}");
                        Notification::make()
                            ->title("The job with uuid '{$record->uuid}' has been pushed back onto the queue.")
                            ->success()
                            ->send();
                    }),
                Action::make('delete')
                    ->label('Mark as Resolved')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function (FailedJob $failedJob) {
                        $failedJob->delete();
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('retry')
                    ->label('Retry')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            try {
                                Artisan::call("queue:retry {$record->uuid}");
                            } catch (Exception $e) {
                                report($e);
                                Notification::make()
                                    ->title($e->getMessage())
                                    ->warning()
                                    ->send();
                            }
                        }
                        Notification::make()
                            ->title("{$records->count()} jobs have been pushed back onto the queue.")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('delete')
                    ->label('Mark as Resolved')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $record->delete();
                        }
                        Notification::make()
                            ->title("{$records->count()} jobs have been marked as resolved.")
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('failed_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFailedJobs::route('/'),
        ];
    }
}
