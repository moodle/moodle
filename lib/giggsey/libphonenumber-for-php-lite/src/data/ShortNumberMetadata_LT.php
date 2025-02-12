<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'LT',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[01]\\d(?:\\d(?:\\d{3})?)?',
        'posLength' => [
            2,
            3,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '0(?:11?|22?|33?)|1(?:0[1-3]|1(?:2|6111))|116(?:0\\d|12)\\d',
        'example' => '01',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '0(?:11?|22?|33?)|1(?:0[1-3]|12)',
        'example' => '01',
        'posLength' => [
            2,
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '0(?:11?|22?|33?)|1(?:0[1-3]|1(?:[27-9]|6(?:000|1(?:1[17]|23))))',
        'example' => '01',
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
