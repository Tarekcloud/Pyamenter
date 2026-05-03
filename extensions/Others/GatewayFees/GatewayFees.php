<?php

namespace Paymenter\Extensions\Other\GatewayFees;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Extension;
use App\Models\Extension as ExtensionModel;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

#[ExtensionMeta(
    name: 'Gateway Fees',
    description: 'Automatically add percentage and fixed fees to invoices based on the selected payment method.',
    version: '1.0.0',
    author: 'Paymenter User',
    url: '',
    icon: 'fas fa-percent'
)]
class GatewayFees extends Extension
{
    /**
     * Prevent infinite loops when updating the invoice.
     */
    public static $isProcessing = false;

    public function boot()
    {
        // Listen for Invoice updates
        // We use the eloquent 'updated' event to catch changes to the payment_method column
        Event::listen('eloquent.updated: App\Models\Invoice', function (Invoice $invoice) {
            $this->handleInvoiceUpdate($invoice);
        });

        // We also listen for 'created' in case a payment method is selected immediately upon creation
        Event::listen('eloquent.created: App\Models\Invoice', function (Invoice $invoice) {
            $this->handleInvoiceUpdate($invoice);
        });
    }

    /**
     * Main logic to handle fee calculation and invoice item management.
     */
    protected function handleInvoiceUpdate(Invoice $invoice)
    {
        // 1. Guard Clauses
        if (self::$isProcessing) {
            return;
        }

        // Only process unpaid invoices
        if ($invoice->status !== 'pending' && $invoice->status !== 'draft') {
            return;
        }

        self::$isProcessing = true;

        try {
            // 2. Identify the Fees Item
            // We look for any existing item with our specific description or code
            $existingFeeItem = $invoice->items->first(function ($item) {
                return $item->description === 'Gateway Fee';
            });

            // 3. Determine the Payment Method
            // Paymenter stores the extension ID in the 'payment_method' column (or similar relation)
            $gatewayId = $invoice->payment_method;

            // If no gateway is selected, remove any existing fee and exit
            if (empty($gatewayId)) {
                if ($existingFeeItem) {
                    $existingFeeItem->delete();
                    // Updating the total is usually handled by Invoice model events, 
                    // but we might need to touch the invoice to trigger total recalculation if Paymenter doesn't auto-sum items on delete.
                    // For safety, we rely on the system's next refresh or force a touch if needed.
                }
                return;
            }

            // 4. Calculate the Base Amount (Subtotal without the fee)
            // We sum all items EXCEPT the gateway fee itself to avoid "interest on interest"
            $subtotal = $invoice->items->reject(function ($item) {
                return $item->description === 'Gateway Fee';
            })->sum('total');

            // 5. Calculate the Fee
            $feeAmount = 0;
            
            // Get config for this specific gateway
            $percent = (float) $this->config('gateway_' . $gatewayId . '_percent');
            $fixed = (float) $this->config('gateway_' . $gatewayId . '_fixed');

            if ($percent > 0) {
                $feeAmount += $subtotal * ($percent / 100);
            }
            if ($fixed > 0) {
                $feeAmount += $fixed;
            }

            // Round to 2 decimal places to match currency standards
            $feeAmount = round($feeAmount, 2);

            // 6. Update or Create the Invoice Item
            if ($feeAmount > 0) {
                if ($existingFeeItem) {
                    // Update existing item if amount changed
                    if (abs($existingFeeItem->unit_price - $feeAmount) > 0.001) {
                        $existingFeeItem->update([
                            'unit_price' => $feeAmount,
                            'total' => $feeAmount, // assuming quantity 1
                        ]);
                    }
                } else {
                    // Create new item
                    $invoice->items()->create([
                        'description' => 'Gateway Fee',
                        'unit_price' => $feeAmount,
                        'quantity' => 1,
                        'total' => $feeAmount,
                        'currency_code' => $invoice->currency_code,
                    ]);
                }
            } else {
                // If fee is 0 (e.g. gateway has no fees configured), remove existing item
                if ($existingFeeItem) {
                    $existingFeeItem->delete();
                }
            }

        } catch (\Exception $e) {
            Log::error('GatewayFees Error: ' . $e->getMessage());
        } finally {
            self::$isProcessing = false;
        }
    }

    /**
     * Generate configuration fields dynamically for all installed gateways.
     */
    public function getConfig($values = [])
    {
        $fields = [];

        // Get all enabled gateways
        $gateways = ExtensionModel::where('type', 'gateway')->where('enabled', true)->get();

        if ($gateways->isEmpty()) {
            return [
                [
                    'name' => 'info',
                    'label' => 'No Gateways Found',
                    'type' => 'text',
                    'description' => 'Please enable at least one payment gateway to configure fees.',
                    'disabled' => true,
                    'required' => false,
                ]
            ];
        }

        foreach ($gateways as $gateway) {
            $fields[] = [
                'name' => 'gateway_' . $gateway->id . '_section',
                'label' => $gateway->name . ' Settings',
                'type' => 'label', // Just a visual separator if supported, or generic text
                'description' => 'Fee settings for ' . $gateway->name,
                'required' => false,
            ];

            $fields[] = [
                'name' => 'gateway_' . $gateway->id . '_percent',
                'label' => $gateway->name . ' - Fee Percentage (%)',
                'placeholder' => 'e.g. 3.5',
                'type' => 'text',
                'description' => 'Add this percentage of the subtotal as a fee.',
                'required' => false,
            ];

            $fields[] = [
                'name' => 'gateway_' . $gateway->id . '_fixed',
                'label' => $gateway->name . ' - Fixed Fee',
                'placeholder' => 'e.g. 0.30',
                'type' => 'text',
                'description' => 'Add this fixed amount as a fee.',
                'required' => false,
            ];
        }

        return $fields;
    }
}