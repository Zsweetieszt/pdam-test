<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Express.js WhatsApp service integration using
    | whatsapp-web.js library for sending notifications.
    |
    */

    'service_url' => env('WHATSAPP_SERVICE_URL', 'http://localhost:3000'),

    'timeout' => env('WHATSAPP_TIMEOUT', 30),

    'retry_attempts' => env('WHATSAPP_RETRY_ATTEMPTS', 3),

    'phone_format' => env('WHATSAPP_PHONE_FORMAT', 'id'),

    'default_template' => env('WHATSAPP_DEFAULT_TEMPLATE', 'bill_reminder'),

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Service Endpoints
    |--------------------------------------------------------------------------
    */

    'endpoints' => [
        'send_message' => '/sendmessage',
        'get_qr' => '/getqr',
        'health_check' => '/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Templates
    |--------------------------------------------------------------------------
    */

    'templates' => [
        'bill_reminder' => 'Tagihan PLN bulan {{period}} sebesar Rp {{amount}} jatuh tempo {{due_date}}',
        'overdue_notice' => 'PERINGATAN! Tagihan PLN Anda telah melewati jatuh tempo',
        'payment_confirmation' => 'Pembayaran tagihan PLN Anda sebesar Rp {{amount}} telah diterima',
    ],

    /*
    |--------------------------------------------------------------------------
    | Phone Number Configuration
    |--------------------------------------------------------------------------
    */

    'phone' => [
        'country_code' => '62', // Indonesia
        'format_remove' => ['+', '-', ' ', '(', ')'],
        'prefix_replace' => [
            '0' => '62', // Replace leading 0 with 62
        ],
    ],

];
