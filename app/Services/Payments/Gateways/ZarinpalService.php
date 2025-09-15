<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Models\OrderTransaction;
use App\Services\Payments\Contracts\PaymentInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class ZarinpalService implements PaymentInterface
{
    protected string $merchantId;
    protected string $callbackUrl;
    protected bool $sandbox;
    protected bool $zaringate;
    protected string $baseUrl;

    public function __construct()
    {
        $config = config('services.zarinpal');

        $this->merchantId = $config['merchant_id'];
        $this->callbackUrl = $config['callback_url'];
        $this->sandbox = $config['sandbox'] ?? false;
        $this->zaringate = $config['zaringate'] ?? false;
        $this->baseUrl = $config['base_url'];
    }

    public function pay2(Order $order): array
    {
        // 1. Check if there's already a pending transaction for this order
        $existingTransaction = $order->transactions()
            ->where('status', 'pending')
            ->where('gateway', $this->getGatewayName())
            ->first();

        if ($existingTransaction) {
            // Return the same redirect URL instead of creating a new one
            $authority = $existingTransaction->transaction_id;
            $redirectUrl = $this->zaringate
                ? "https://www.zarinpal.com/pg/StartPay/{$authority}/ZarinGate"
                : "https://www.zarinpal.com/pg/StartPay/{$authority}";

            return [
                'redirect_url' => $redirectUrl,
                'authority' => $authority,
            ];
        }

        // 2. Build payload for new transaction
        $payload = [
            'merchant_id' => $this->merchantId,
            'amount' => round($order->total),
            'callback_url' => $this->callbackUrl,
            'description' => "Order #" . $order->order_number,
            'metadata' => [
                'email' => optional($order->customer)->email,
                'mobile' => optional($order->customer)->phone,
            ],
        ];

        // 3. Send payment request to Zarinpal
        $response = Http::post("{$this->baseUrl}/payment/request.json", $payload);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        $body = $response->json()['data'] ?? [];

        if (!isset($body['authority'])) {
            throw new \Exception('Zarinpal request failed: No authority found');
        }

        // 4. Save initial transaction record
        $order->transactions()->create([
            'transaction_id' => $body['authority'],
            'status' => 'pending',
            'payment_method' => 'online',
            'amount' => $order->total,
            'gateway' => $this->getGatewayName(),
            'meta' => $payload,
            'payload' => $body,
        ]);

        $redirectUrl = $this->zaringate
            ? "https://www.zarinpal.com/pg/StartPay/{$body['authority']}/ZarinGate"
            : "https://www.zarinpal.com/pg/StartPay/{$body['authority']}";

        return [
            'redirect_url' => $redirectUrl,
            'authority' => $body['authority'],
        ];
    }

    public function pay(Order $order): array
    {
        // 1. Check if there's already a valid pending transaction for this order
        $existingTransaction = $order->transactions()
            ->where('status', 'pending')
            ->where('gateway', $this->getGatewayName())
            ->latest()
            ->first();

        if ($existingTransaction) {
            $expiresAt = data_get($existingTransaction->meta, 'expires_at');

            // if not expired, reuse
            if ($expiresAt && now()->lt(\Carbon\Carbon::parse($expiresAt))) {
                $authority = $existingTransaction->transaction_id;
                $redirectUrl = $this->zaringate
                    ? "https://www.zarinpal.com/pg/StartPay/{$authority}/ZarinGate"
                    : "https://www.zarinpal.com/pg/StartPay/{$authority}";

                return [
                    'redirect_url' => $redirectUrl,
                    'authority' => $authority,
                ];
            }

            // otherwise mark it as expired
            $existingTransaction->update(['status' => 'expired']);
        }

        // 2. Build payload for new transaction
        $payload = [
            'merchant_id' => $this->merchantId,
            'amount' => round($order->total),
            'callback_url' => $this->callbackUrl,
            'description' => "Order #" . $order->order_number,
            'metadata' => [
                'email' => optional($order->customer)->email,
                'mobile' => optional($order->customer)->phone,
            ],
        ];

        // 3. Send payment request to Zarinpal
        $response = Http::post("{$this->baseUrl}/payment/request.json", $payload);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        $body = $response->json()['data'] ?? [];

        if (!isset($body['authority'])) {
            throw new \Exception('Zarinpal request failed: No authority found');
        }

        // 4. Save initial transaction record with expiry (30 min by default)
        $meta = array_merge($payload, [
            'expires_at' => now()->addMinutes(30)->toDateTimeString(),
        ]);

        $order->transactions()->create([
            'transaction_id' => $body['authority'],
            'status' => 'pending',
            'payment_method' => 'online',
            'amount' => $order->total,
            'gateway' => $this->getGatewayName(),
            'meta' => $meta,
            'payload' => $body,
        ]);

        $redirectUrl = $this->zaringate
            ? "https://www.zarinpal.com/pg/StartPay/{$body['authority']}/ZarinGate"
            : "https://www.zarinpal.com/pg/StartPay/{$body['authority']}";

        return [
            'redirect_url' => $redirectUrl,
            'authority' => $body['authority'],
        ];
    }

    public function verify(Order $order, array $data): bool
    {
        $payload = [
            'merchant_id' => $this->merchantId,
            'amount' => round($order->total),
            'authority' => $data['Authority'],
        ];

        $response = Http::post("{$this->baseUrl}/payment/verify.json", $payload);

//        if ($response->failed()) {
//            throw new RequestException($response);
//        }

        $body = $response->json()['data'] ?? [];

        $success = isset($body['code']) && $body['code'] == 100;

        // Update or create transaction
        $order->transactions()->updateOrCreate(
            ['transaction_id' => $data['Authority']],
            [
                'status' => $success ? 'success' : 'failed',
                'payment_method' => 'online',
                'amount' => $order->total,
                'gateway' => $this->getGatewayName(),
                'paid_at' => $success ? now() : null,
                'payload' => $body,
            ]
        );

        return $success;
    }

    public function getGatewayName(): string
    {
        return 'zarinpal';
    }
}
