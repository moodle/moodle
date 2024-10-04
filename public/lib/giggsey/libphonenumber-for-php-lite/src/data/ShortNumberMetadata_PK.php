<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'PK',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '1\\d{1,3}',
        'posLength' => [
            2,
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1(?:2\\d?|5)|[56])',
        'example' => '15',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:1(?:22?|5)|[56])',
        'example' => '15',
    ],
    'shortCode' => [
        'pattern' => '1(?:122|3[014]|[56])|11[2457-9]',
        'example' => '15',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'posLength' => [
            -1,
        ],
    ],
    'smsServices' => [
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
