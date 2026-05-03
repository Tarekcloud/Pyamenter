<?php

namespace Paymenter\Extensions\Gateways\Alipay;

use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class Alipay extends Gateway
{
    public function boot()
    {
        require __DIR__ . '/routes.php';
        View::addNamespace('gateways.alipay', __DIR__ . '/resources/views');
    }

    public function getConfig($values = [])
    {
        return [
            ['name' => 'alipay_app_id', 'label' => 'AppID', 'type' => 'text', 'required' => true],
            ['name' => 'alipay_private_key', 'label' => '应用私钥', 'type' => 'textarea', 'required' => true],
            ['name' => 'alipay_public_key', 'label' => '支付宝公钥', 'type' => 'textarea', 'required' => true],
        ];
    }

    public function pay($invoice, $total)
    {
        $outTradeNo = time() . str_pad(mt_rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);

        $bizContent = [
            'out_trade_no' => $outTradeNo,
            'total_amount' => number_format($total, 2, '.', ''),
            'subject' => 'Invoice #' . $invoice->id,
            'product_code' => 'QR_CODE_OFFLINE',
        ];

        $params = [
            'app_id' => $this->config('alipay_app_id'),
            'method' => 'alipay.trade.precreate',
            'format' => 'JSON',
            'charset' => 'utf-8',
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'notify_url' => route('extensions.gateways.alipay.webhook'),
            'biz_content' => json_encode($bizContent),
        ];

        $response = $this->createOrder($params);

        if (empty($response['qr_code'])) {
            throw new Exception('支付宝下单失败：' . json_encode($response));
        }

        return view('gateways.alipay::pay', compact('invoice', 'total') + [
            'qr_code' => $response['qr_code'],
            'out_trade_no' => $outTradeNo,
        ]);
    }

    private function createOrder($params)
    {
        $params['sign'] = $this->generateSign($params);

        $url = 'https://openapi.alipay.com/gateway.do';

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $response = Http::asForm()
                    ->connectTimeout(15)
                    ->timeout(45)
                    ->post($url, $params);

                if ($response->successful()) {
                    $result = $response->json();
                    $responseKey = 'alipay_trade_precreate_response';

                    if (isset($result[$responseKey])) {
                        $tradeResponse = $result[$responseKey];

                        if ($tradeResponse['code'] === '10000') {
                            return $tradeResponse;
                        } else {
                            throw new Exception('支付宝API错误：' . ($tradeResponse['sub_msg'] ?? $tradeResponse['msg']));
                        }
                    }
                }

                if ($attempt === 3) {
                    throw new Exception('支付宝请求失败：' . $response->body());
                }
            } catch (Exception $e) {
                if ($attempt === 3) {
                    throw new Exception('支付宝下单失败：' . $e->getMessage());
                }
                sleep($attempt);
            }
        }
    }

    private function generateSign($params)
    {
        unset($params['sign']);
        ksort($params);

        $signString = '';
        foreach ($params as $key => $value) {
            if ($value !== '' && $value !== null) {
                $signString .= $key . '=' . $value . '&';
            }
        }
        $signString = rtrim($signString, '&');

        $privateKey = $this->config('alipay_private_key');
        if (strpos($privateKey, '-----BEGIN') === false) {
            $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" . chunk_split($privateKey, 64, "\n") . "-----END RSA PRIVATE KEY-----";
        }

        openssl_sign($signString, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    public function checkPaymentStatus($invoice)
    {
        $invoice = Invoice::findOrFail($invoice);
        return response()->json([
            'paid' => $invoice->status === 'paid',
            'status' => $invoice->status,
        ]);
    }

    public function webhook(Request $request)
    {
        if (!$this->verifyNotifySign($request->all())) {
            return response('FAIL', 400);
        }

        $data = $request->all();

        if (($data['notify_type'] ?? null) !== 'trade_status_sync') {
            return response('SUCCESS', 200);
        }

        $subject = $data['subject'] ?? '';
        if (!preg_match('/Invoice #(\d+)/', $subject, $matches)) {
            return response('FAIL', 400);
        }

        $invoiceId = $matches[1];
        $invoice = Invoice::find($invoiceId);
        if (!$invoice || $invoice->status === 'paid') {
            return response('SUCCESS', 200);
        }

        if (($data['trade_status'] ?? null) === 'TRADE_SUCCESS') {
            ExtensionHelper::addPayment(
                $invoice->id,
                'Alipay',
                floatval($data['total_amount'] ?? 0),
                null,
                $data['trade_no'] ?? null
            );
        }

        return response('SUCCESS', 200);
    }

    private function verifyNotifySign($params)
    {
        $sign = $params['sign'] ?? '';
        $signType = $params['sign_type'] ?? '';

        if (!$sign || $signType !== 'RSA2') {
            return false;
        }

        unset($params['sign'], $params['sign_type']);
        ksort($params);

        $signString = '';
        foreach ($params as $key => $value) {
            if ($value !== '' && $value !== null) {
                $signString .= $key . '=' . $value . '&';
            }
        }
        $signString = rtrim($signString, '&');

        $publicKey = $this->config('alipay_public_key');
        if (strpos($publicKey, '-----BEGIN') === false) {
            $publicKey = "-----BEGIN PUBLIC KEY-----\n" . chunk_split($publicKey, 64, "\n") . "-----END PUBLIC KEY-----";
        }

        return openssl_verify($signString, base64_decode($sign), $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }
}
