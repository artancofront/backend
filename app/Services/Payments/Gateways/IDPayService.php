<?php

namespace App\Services\Payments\Gateways;

use App\Models\Order;
use App\Models\OrderTransaction;
use App\Services\Payments\Contracts\PaymentInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class IDPayService implements PaymentInterface
{
    protected string $apiKey;
    protected string $callbackUrl;
    protected string $baseUrl;
    protected bool $sandbox;

    public function __construct()
    {
        $config = config('services.idpay');

        $this->apiKey = $config['api_key'];
        $this->callbackUrl = $config['callback_url'];
        $this->baseUrl = $config['base_url'] ?? 'https://api.idpay.ir/v1.1';
        $this->sandbox = $config['sandbox'] ?? false;
    }

    public function pay(Order $order): array
    {
        $payload = [
            'order_id' => $order->order_number,
            'amount' => $order->total,
            'name' => optional($order->customer)->name,
            'phone' => optional($order->customer)->phone,
            'mail' => optional($order->customer)->email,
            'desc' => "Order #" . $order->order_number,
            'callback' => $this->callbackUrl,
        ];

        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
            'X-SANDBOX' => $this->sandbox ? '1' : '0',
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/payment", $payload);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        $body = $response->json();

        // Save initial transaction record
        $order->transactions()->create([
            'transaction_id' => $body['id'] ?? null,
            'status' => 'pending',
            'payment_method' => 'online',
            'amount' => $order->total,
            'gateway' => $this->getGatewayName(),
            'meta' => $payload,
            'payload' => $body,
        ]);

        return [
            'redirect_url' => $body['link'] ?? null,
            'id' => $body['id'] ?? null,
        ];
    }

    public function verify(Order $order, array $data): bool
    {
        $payload = [
            'id' => $data['id'],
            'order_id' => $order->order_number,
        ];

        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey,
            'X-SANDBOX' => $this->sandbox ? '1' : '0',
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/payment/verify", $payload);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        $body = $response->json();

        $success = isset($body['status']) && $body['status'] == 100;

        $order->transactions()->updateOrCreate(
            ['transaction_id' => $body['id'] ?? $data['id']],
            [
                'status' => $success ? 'success' : 'failed',
                'payment_method' => 'online',
                'amount' => $body['amount'] ?? $order->total,
                'gateway' => $this->getGatewayName(),
                'paid_at' => $success ? now() : null,
                'payload' => $body,
            ]
        );

        return $success;
    }

    public function getGatewayName(): string
    {
        return 'idpay';
    }
}
