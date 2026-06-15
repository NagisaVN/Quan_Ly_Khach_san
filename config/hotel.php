<?php

return [
    'tax_rate' => (float) env('HOTEL_TAX_RATE', 0.10),
    'payment_driver' => env('PAYMENT_DRIVER', 'mock'),
];
