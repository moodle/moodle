<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CY',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '1\\d\\d(?:\\d{3})?',
        'posLength' => [
            3,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1(?:2|6\\d{3})|99)',
        'example' => '112',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:12|99)',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1(?:2|6(?:000|111))|99)',
        'example' => '112',
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
