<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'idpay' => [
        'api_key' => env('IDPAY_API_KEY'),
        'callback_url' => env('IDPAY_CALLBACK_URL'),
        'sandbox' => env('IDPAY_SANDBOX', true),
        'base_url' => env('IDPAY_BASE_URL', 'https://api.idpay.ir/v1.1'),
    ],
    'zarinpal' => [
        'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
        'callback_url' => env('ZARINPAL_CALLBACK_URL'),
        'sandbox' => env('ZARINPAL_SANDBOX', true),
        'zaringate' => env('ZARINPAL_ZARINGATE', false),
        'base_url' => env('ZARINPAL_BASE_URL', 'https://api.zarinpal.com/pg/v4'),
    ],


];
