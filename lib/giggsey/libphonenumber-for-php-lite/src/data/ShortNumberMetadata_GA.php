<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GA',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '1\\d(?:\\d{2})?',
        'posLength' => [
            2,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '18|1(?:3\\d|73)\\d',
        'example' => '18',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:3\\d\\d|730|8)',
        'example' => '18',
    ],
    'shortCode' => [
        'pattern' => '1(?:3\\d\\d|730|8)',
        'example' => '18',
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
