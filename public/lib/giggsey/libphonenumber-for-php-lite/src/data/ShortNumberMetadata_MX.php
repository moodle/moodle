<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MX',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[0579]\\d{2,4}',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '0(?:6[0568]|80)|911',
        'example' => '060',
        'posLength' => [
            3,
        ],
    ],
    'premiumRate' => [
        'pattern' => '(?:530\\d|776)\\d',
        'example' => '7760',
        'posLength' => [
            4,
            5,
        ],
    ],
    'emergency' => [
        'pattern' => '0(?:6[0568]|80)|911',
        'example' => '060',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '0[1-9]\\d|53053|7766|911',
        'example' => '010',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '0(?:[249]0|[35][01])',
        'example' => '020',
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
