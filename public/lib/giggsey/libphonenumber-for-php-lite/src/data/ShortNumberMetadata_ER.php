<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'ER',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[12]\\d\\d(?:\\d{3})?',
        'posLength' => [
            3,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '11[2-46]|(?:12[47]|20[12])\\d{3}',
        'example' => '112',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:1[2-46]|24422)|20(?:1(?:606|917)|2914)|(?:1277|2020)99',
        'example' => '112',
    ],
    'shortCode' => [
        'pattern' => '1(?:1[2-6]|24422)|20(?:1(?:606|917)|2914)|(?:1277|2020)99',
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
