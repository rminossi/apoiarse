<?php

return [

    'asaas' => [
        'api_key' => env('ASAAS_API_KEY'),
        'webhook_token' => env('ASAAS_WEBHOOK_TOKEN', env('ASAAS_API_KEY')),
        'base_url' => env('ASAAS_BASE_URL', 'https://api.asaas.com/v3'),
    ],

    'mercadopago' => [
        'access_token' => env('MP_ACCESS_TOKEN'),
        'notification_url' => env('MP_NOTIFICATION_URL'),
    ],

    'defaults' => [
        'platform_fee_percent' => (float) env('PAYMENT_PLATFORM_FEE_PERCENT', 5),
        'platform_fee_fixed' => (float) env('PAYMENT_PLATFORM_FEE_FIXED', 0),
        'mp_pix_max_amount' => (float) env('PAYMENT_MP_PIX_MAX_AMOUNT', 200),
        'mp_pix_fee_percent' => (float) env('PAYMENT_MP_PIX_FEE_PERCENT', 0.99),
        'asaas_pix_fee_fixed' => (float) env('PAYMENT_ASAAS_PIX_FEE_FIXED', 1.99),
        'asaas_card_fee_fixed' => (float) env('PAYMENT_ASAAS_CARD_FEE_FIXED', 0.49),
        'asaas_card_fee_percent' => (float) env('PAYMENT_ASAAS_CARD_FEE_PERCENT', 2.99),
    ],

];
