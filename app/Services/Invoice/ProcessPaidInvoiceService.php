<?php

namespace App\Services\Invoice;

use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use App\Services\Service\RenewServiceService;
use App\Services\ServiceUpgrade\ServiceUpgradeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessPaidInvoiceService
{
    /**
     * Handle the processing of a paid invoice.
     */
    public function handle(Invoice $invoice): void
    {
        // 核心修复逻辑：引入幂等性（Idempotency Key）防并发
        // Cache::add 是原子操作。哪怕两次请求在同一毫秒到达，也只有一次能返回 true。
        // 这里我们将该账单标记为已处理，有效期 24 小时。
        $idempotencyKey = 'invoice_processed_' . $invoice->id;
        
        if (!Cache::add($idempotencyKey, true, now()->addHours(24))) {
            // 如果返回 false，说明这个账单在短时间内已经被处理过了，直接拦截！
            Log::warning("防重防御触发：成功拦截到重复的发货/充值请求，Invoice ID: {$invoice->id}");
            return;
        }

        // Update services if invoice is paid (suspended -> active etc.)
        $invoice->items->each(function ($item) use ($invoice) 
        {
            if ($item->reference_type == Service::class) 
            {
                $service = $item->reference;
                
                if (!$service || !($service instanceof Service)) 
                {
                    return;
                }
                
                (new RenewServiceService)->handle($service);
            } 
            elseif ($item->reference_type == ServiceUpgrade::class) 
            {
                $serviceUpgrade = $item->reference;
                if (!$serviceUpgrade || $serviceUpgrade->status !== ServiceUpgrade::STATUS_PENDING || !($serviceUpgrade instanceof ServiceUpgrade)) 
                {
                    return;
                }

                // Handle the upgrade
                (new ServiceUpgradeService)->handle($serviceUpgrade);
            } 
            elseif ($item->reference_type == Credit::class) 
            {
                // Check if user has credits in this currency
                $user = $invoice->user;
                $credit = $user->credits()->where('currency_code', $invoice->currency_code)->first();

                if ($credit) 
                {
                    $credit->amount += $item->price;
                    $credit->save();
                } 
                else 
                {
                    $user->credits()->create([
                        'currency_code' => $invoice->currency_code,
                        'amount' => $item->price,
                    ]);
                }
            }
        });
    }
}