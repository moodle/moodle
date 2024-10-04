<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MV',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[14]\\d{2,3}',
        'posLength' => [
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:02|1[89])',
        'example' => '102',
        'posLength' => [
            3,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:02|1[89])',
        'example' => '102',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:[0-37-9]|[4-6]\\d)\\d|4040|1[45]1',
        'example' => '100',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '1[45]1',
        'example' => '141',
        'posLength' => [
            3,
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
