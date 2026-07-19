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

];
