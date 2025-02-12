<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'IL',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[12]\\d{2,4}',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:0[0-2]|12)',
        'example' => '100',
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
        'pattern' => '1(?:0[0-2]|12)',
        'example' => '100',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0[0-2]|1(?:[013-9]\\d|2)|[2-9]\\d\\d)|2407|(?:104|27)00',
        'example' => '100',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '104\\d\\d',
        'example' => '10400',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '104\\d\\d',
        'example' => '10400',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
