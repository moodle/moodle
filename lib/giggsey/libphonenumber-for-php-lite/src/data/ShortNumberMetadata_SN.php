<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SN',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[12]\\d{1,5}',
        'posLength' => [
            2,
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:515|[78])|2(?:00|1)\\d{3}',
        'example' => '17',
        'posLength' => [
            2,
            4,
            5,
            6,
        ],
    ],
    'premiumRate' => [
        'pattern' => '2(?:0[246]|[468])\\d{3}',
        'example' => '24000',
        'posLength' => [
            5,
            6,
        ],
    ],
    'emergency' => [
        'pattern' => '1[78]',
        'example' => '17',
        'posLength' => [
            2,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1[69]|(?:[246]\\d|51)\\d)|2(?:0[0-246]|[12468])\\d{3}|1[278]',
        'example' => '12',
    ],
    'standardRate' => [
        'pattern' => '2(?:01|2)\\d{3}',
        'example' => '22000',
        'posLength' => [
            5,
            6,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '1[46]\\d\\d',
        'example' => '1400',
        'posLength' => [
            4,
        ],
    ],
    'smsServices' => [
        'pattern' => '2[468]\\d{3}',
        'example' => '24000',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
