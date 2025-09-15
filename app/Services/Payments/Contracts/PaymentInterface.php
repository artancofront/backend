<?php

namespace App\Services\Payments\Contracts;

use App\Models\Order;

interface PaymentInterface
{
    public function pay(Order $order): array;

    public function verify(Order $order, array $data): bool;

    public function getGatewayName(): string;
}
