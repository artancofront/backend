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

    public function pay(Order $order): array
    {
        $payload = [
            'merchant_id' => $this->merchantId,
            'amount' => $order->total,
            'callback_url' => $this->callbackUrl,
            'description' => "Order #" . $order->order_number,
            'metadata' => [
                'email' => optional($order->customer)->email,
                'mobile' => optional($order->customer)->phone,
            ],
        ];

        $response = Http::post("{$this->baseUrl}/payment/request.json", $payload);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        $body = $response->json()['data'] ?? [];

        if (!isset($body['authority'])) {
            throw new \Exception('Zarinpal request failed: No authority found');
        }

        // Save initial transaction record
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

    public function verify(Order $order, array $data): bool
    {
        $payload = [
            'merchant_id' => $this->merchantId,
            'amount' => $order->total,
            'authority' => $data['Authority'],
        ];

        $response = Http::post("{$this->baseUrl}/payment/verify.json", $payload);

        if ($response->failed()) {
            throw new RequestException($response);
        }

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
