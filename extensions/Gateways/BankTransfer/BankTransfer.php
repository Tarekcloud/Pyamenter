<?php

namespace Paymenter\Extensions\Gateways\BankTransfer;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use App\Models\Credit;
use App\Models\Service;
use Illuminate\Support\Facades\View;

#[ExtensionMeta(
    name: 'Bank Transfer Gateway',
    description: 'Payments by manual bank transfer with instructions and reference and discount system',
    version: '1.0.0',
    author: 'Fliqs',
    icon: 'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iNTEyIiBoZWlnaHQ9IjUxMiIgcng9IjcyIiBmaWxsPSIjMTYzMzAwIi8+CiAgPHBhdGggZD0iTTExNiAxNTZMMTc2IDM1NkwyNTYgMjEyTDMzNiAzNTZMMzk2IDE1NiIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSI0MiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+CiAgPHBhdGggZD0iTTI5MiAyMTJIMzY0IiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjM0IiBzdHJva2UtbGluZWNhcD0icm91bmQiLz4KICA8cGF0aCBkPSJNMzM0IDE3MEwzODggMjEyTDMzNCAyNTQiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMzQiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgo8L3N2Zz4='
)]
class BankTransfer extends Gateway
{
    public function boot()
    {
        if (file_exists(__DIR__ . '/routes.php')) {
            require __DIR__ . '/routes.php';
        }
        View::addNamespace('gateways.banktransfer', __DIR__ . '/resources/views');
    }

    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'account_holder',
                'label' => 'Account Holder',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'bank_name',
                'label' => 'Bank Name',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'iban',
                'label' => 'IBAN',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'bic',
                'label' => 'Swift/BIC',
                'type' => 'text',
                'required' => false,
            ],
            [
                'name' => 'bank_address',
                'label' => 'Bank Address',
                'type' => 'textarea',
                'required' => false,
            ],
            [
                'name' => 'instructions',
                'label' => 'Additional Instructions',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'additional instructions',
            ],
            [
                'name' => 'days_to_pay',
                'label' => 'payment deadline (days)',
                'type' => 'number',
                'required' => false,
            ],
            [
                'name' => 'reference_prefix',
                'label' => 'reference prefix',
                'type' => 'text',
                'required' => false,
                'placeholder' => 'INV-',
            ],
            [
                'name' => 'discount_enabled',
                'label' => 'enable 5% bank transfer discount',
                'type' => 'checkbox',
                'required' => false,
                'database_type' => 'boolean',
            ],
            [
                'name' => 'discount_percentage',
                'label' => 'discount percentage',
                'type' => 'number',
                'required' => false,
            ],
            [
                'name' => 'discount_exclude_products',
                'label' => 'exclude products from discount (IDs, comma-separated)',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'e.g. 12,34,56',
            ],
        ];
    }

    public function pay(Invoice $invoice, $total)
    {
        $prefix = $this->config('reference_prefix') ?: 'INV-';
        $reference = $invoice->number ?: ($prefix . $invoice->id);

        $discountEnabled = $this->config('discount_enabled');
        $discountEnabled = is_null($discountEnabled) ? true : (bool) $discountEnabled;
        $percentage = $this->config('discount_percentage');
        $percentage = is_null($percentage) ? 5 : (float) $percentage;
        $percentage = max(0, min(100, $percentage));
        if ($invoice->items()->where('reference_type', Credit::class)->exists()) {
            $discountEnabled = false;
        }
        $excludeRaw = (string) ($this->config('discount_exclude_products') ?? '');
        $excludeIds = collect(explode(',', $excludeRaw))
            ->map(fn ($v) => (int) trim($v))
            ->filter(fn ($v) => $v > 0)
            ->values()
            ->all();

        if (!empty($excludeIds)) {
            foreach ($invoice->items as $item) {
                if ($item->reference_type === Service::class) {
                    $service = $item->reference;
                    if ($service && $service->product && in_array($service->product->id, $excludeIds)) {
                        $discountEnabled = false;
                        break;
                    }
                }
            }
        }
        $discountAmount = $discountEnabled ? round($total * ($percentage / 100), 2) : 0;
        $discountedTotal = round($total - $discountAmount, 2);

        ExtensionHelper::addProcessingPayment($invoice->id, 'BankTransfer', $discountedTotal, null, $reference);

        return view('gateways.banktransfer::pay', [
            'invoice' => $invoice,
            'total' => $discountedTotal,
            'originalTotal' => $total,
            'discountAmount' => $discountAmount,
            'discountPercentage' => $percentage,
            'currency' => $invoice->currency_code,
            'reference' => $reference,
            'accountHolder' => $this->config('account_holder'),
            'bankName' => $this->config('bank_name'),
            'iban' => $this->config('iban'),
            'bic' => $this->config('bic'),
            'bankAddress' => $this->config('bank_address'),
            'instructions' => $this->config('instructions'),
            'daysToPay' => $this->config('days_to_pay'),
        ]);
    }
}