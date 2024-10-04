<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'PG',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[01]\\d{2,6}',
        'posLength' => [
            3,
            4,
            5,
            6,
            7,
        ],
    ],
    'tollFree' => [
        'pattern' => '000|11[01]',
        'example' => '000',
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
        'pattern' => '000|11[01]',
        'example' => '000',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '000|1(?:1[01]|5\\d\\d|6\\d{2,5})',
        'example' => '000',
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
        'pattern' => '16\\d{2,5}',
        'example' => '1600',
        'posLength' => [
            4,
            5,
            6,
            7,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
