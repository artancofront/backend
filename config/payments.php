<?php
// All available payment gateways are listed here
return [
    'gateways' => [
        'zarinpal' => \App\Services\Payments\Gateways\ZarinpalService::class,
        'idpay' => \App\Services\Payments\Gateways\IDPayService::class,

    ]
];
