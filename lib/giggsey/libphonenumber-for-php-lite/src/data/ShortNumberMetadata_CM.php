<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CM',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[18]\\d{1,3}',
        'posLength' => [
            2,
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1[37]|[37])',
        'example' => '13',
        'posLength' => [
            2,
            3,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:1[37]|[37])',
        'example' => '13',
        'posLength' => [
            2,
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1[37]|[37])|8711',
        'example' => '13',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '871\\d',
        'example' => '8710',
        'posLength' => [
            4,
        ],
    ],
    'smsServices' => [
        'pattern' => '871\\d',
        'example' => '8710',
        'posLength' => [
            4,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
