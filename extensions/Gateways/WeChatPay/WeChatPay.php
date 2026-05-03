<?php

namespace Paymenter\Extensions\Gateways\WeChatPay;

use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class WeChatPay extends Gateway
{
    public function boot()
    {
        require __DIR__ . '/routes.php';
        View::addNamespace('gateways.wechatpay', __DIR__ . '/resources/views');
    }

    public function getConfig($values = [])
    {
        return [
            ['name' => 'wechatpay_appid', 'label' => 'AppID', 'type' => 'text', 'required' => true],
            ['name' => 'wechatpay_mchid', 'label' => '商户号', 'type' => 'text', 'required' => true],
            ['name' => 'wechatpay_private_key', 'label' => '商户私钥', 'type' => 'textarea', 'required' => true],
            ['name' => 'wechatpay_serial_no', 'label' => '证书序列号', 'type' => 'text', 'required' => true],
            ['name' => 'wechatpay_api_v3_key', 'label' => 'APIv3密钥', 'type' => 'text', 'required' => true],
        ];
    }

    public function pay($invoice, $total)
    {
        $outTradeNo = time() . str_pad(mt_rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT);
        $timeExpire = date('Y-m-d\TH:i:s+08:00', time() + 1800);

        $orderData = [
            'appid' => $this->config('wechatpay_appid'),
            'mchid' => $this->config('wechatpay_mchid'),
            'description' => 'Invoice #' . $invoice->id,
            'out_trade_no' => $outTradeNo,
            'time_expire' => $timeExpire,
            'notify_url' => route('extensions.gateways.wechatpay.webhook'),
            'attach' => (string) $invoice->id,
            'amount' => [
                'total' => intval($total * 100),
                'currency' => 'CNY',
            ],
        ];

        $response = $this->createOrder($orderData);

        if (empty($response['code_url'])) {
            throw new Exception('微信支付下单失败：' . json_encode($response));
        }

        return view('gateways.wechatpay::pay', compact('invoice', 'total') + [
            'code_url' => $response['code_url'],
            'out_trade_no' => $outTradeNo,
        ]);
    }

    private function createOrder($orderData)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/native';
        $authorization = $this->generateAuthorization('POST', '/v3/pay/transactions/native', json_encode($orderData));

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $authorization,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->connectTimeout(15)->timeout(45)->post($url, $orderData);

                if ($response->successful()) {
                    return $response->json();
                }

                if ($attempt === 3) {
                    throw new Exception('微信支付请求失败：' . $response->body());
                }
            } catch (Exception $e) {
                if ($attempt === 3) {
                    throw new Exception('微信支付下单失败：' . $e->getMessage());
                }
                sleep($attempt);
            }
        }
    }

    private function generateAuthorization($method, $url, $body = '')
    {
        $timestamp = time();
        $nonce = Str::random(32);
        $signString = $method . "\n" . $url . "\n" . $timestamp . "\n" . $nonce . "\n" . $body . "\n";

        $privateKey = $this->config('wechatpay_private_key');
        if (strpos($privateKey, '-----BEGIN') === false) {
            $privateKey = "-----BEGIN PRIVATE KEY-----\n" . chunk_split($privateKey, 64, "\n") . "-----END PRIVATE KEY-----";
        }

        openssl_sign($signString, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return sprintf(
            'WECHATPAY2-SHA256-RSA2048 mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $this->config('wechatpay_mchid'),
            $nonce,
            $timestamp,
            $this->config('wechatpay_serial_no'),
            base64_encode($signature)
        );
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
        if (!$this->verifySignature($request)) {
            return response()->json(['code' => 'FAIL', 'message' => '签名验证失败'], 400);
        }

        $data = $request->json()->all();
        if (($data['event_type'] ?? null) !== 'TRANSACTION.SUCCESS') {
            return response()->json(['code' => 'SUCCESS'], 200);
        }

        $decrypted = $this->decryptResource($data['resource'] ?? []);
        if (!($decrypted['attach'] ?? null)) {
            return response()->json(['code' => 'FAIL', 'message' => '数据解密失败'], 400);
        }

        $invoice = Invoice::find($decrypted['attach']);
        if (!$invoice || $invoice->status === 'paid') {
            return response()->json(['code' => 'SUCCESS'], 200);
        }

        if (($decrypted['trade_state'] ?? null) === 'SUCCESS') {
            ExtensionHelper::addPayment(
                $invoice->id,
                'WeChatPay',
                $decrypted['amount']['total'] / 100,
                null,
                $decrypted['transaction_id']
            );
        }

        return response()->json(['code' => 'SUCCESS'], 200);
    }

    private function verifySignature(Request $request)
    {
        $timestamp = $request->header('Wechatpay-Timestamp');
        $nonce = $request->header('Wechatpay-Nonce');
        $signature = $request->header('Wechatpay-Signature');
        $serial = $request->header('Wechatpay-Serial');
        return $timestamp && $nonce && $signature && $serial;
    }

    private function createAesUtil()
    {
        $apiKey = $this->config('wechatpay_api_v3_key');

        return new class ($apiKey) {
            private $aesKey;
            const KEY_LENGTH_BYTE = 32;
            const AUTH_TAG_LENGTH_BYTE = 16;

            public function __construct($aesKey)
            {
                if (strlen($aesKey) != self::KEY_LENGTH_BYTE) {
                    throw new \InvalidArgumentException('无效的ApiV3Key，长度应为32个字节');
                }
                $this->aesKey = $aesKey;
            }

            public function decryptToString($associatedData, $nonceStr, $ciphertext)
            {
                $ciphertext = \base64_decode($ciphertext);
                if (strlen($ciphertext) <= self::AUTH_TAG_LENGTH_BYTE) {
                    return false;
                }

                if (function_exists('\sodium_crypto_aead_aes256gcm_is_available') && \sodium_crypto_aead_aes256gcm_is_available()) {
                    return \sodium_crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $this->aesKey);
                }

                if (function_exists('\Sodium\crypto_aead_aes256gcm_is_available') && \Sodium\crypto_aead_aes256gcm_is_available()) {
                    return \Sodium\crypto_aead_aes256gcm_decrypt($ciphertext, $associatedData, $nonceStr, $this->aesKey);
                }

                if (PHP_VERSION_ID >= 70100 && in_array('aes-256-gcm', \openssl_get_cipher_methods())) {
                    $ctext = substr($ciphertext, 0, -self::AUTH_TAG_LENGTH_BYTE);
                    $authTag = substr($ciphertext, -self::AUTH_TAG_LENGTH_BYTE);
                    return \openssl_decrypt($ctext, 'aes-256-gcm', $this->aesKey, \OPENSSL_RAW_DATA, $nonceStr, $authTag, $associatedData);
                }

                throw new \RuntimeException('AEAD_AES_256_GCM需要PHP 7.1以上或者安装libsodium-php');
            }
        };
    }

    private function decryptResource($resource)
    {
        $ciphertext = $resource['ciphertext'] ?? '';
        $nonce = $resource['nonce'] ?? '';
        $ad = $resource['associated_data'] ?? '';
        if (!$ciphertext || !$nonce) {
            return null;
        }

        try {
            $json = $this->createAesUtil()->decryptToString($ad, $nonce, $ciphertext);
            return $json ? json_decode($json, true) : null;
        } catch (Exception $e) {
            return null;
        }
    }
}
