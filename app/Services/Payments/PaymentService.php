<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Services\Payments\Contracts\PaymentInterface;

class PaymentService
{
    protected array $gateways;

    public function __construct(iterable $gateways)
    {
        foreach ($gateways as $gateway) {
            $this->gateways[$gateway->getGatewayName()] = $gateway;
        }
    }

    public function pay(Order $order, string $gatewayName): array
    {
        $gateway = $this->getGateway($gatewayName);
        return $gateway->pay($order);
    }

    public function verify(Order $order, string $gatewayName, array $data): bool
    {
        $gateway = $this->getGateway($gatewayName);
        $success= $gateway->verify($order, $data);
        if($success){
            $order->update(['payment_status' => 'paid']);
        }
        return $success;
    }

    protected function getGateway(string $name): PaymentInterface
    {
        if (!isset($this->gateways[$name])) {
            throw new \InvalidArgumentException("Unsupported payment gateway: $name");
        }
        return $this->gateways[$name];
    }
}
