<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CU',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[12]\\d\\d(?:\\d{3,4})?',
        'posLength' => [
            3,
            6,
            7,
        ],
    ],
    'tollFree' => [
        'pattern' => '10[4-7]|(?:116|204\\d)\\d{3}',
        'example' => '104',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '10[4-6]',
        'example' => '104',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0[4-7]|1(?:6111|8)|40)|2045252',
        'example' => '104',
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
