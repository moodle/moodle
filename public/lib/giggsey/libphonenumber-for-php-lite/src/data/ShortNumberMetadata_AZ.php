<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'AZ',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[148]\\d{2,3}',
        'posLength' => [
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:0[1-3]|12)',
        'example' => '101',
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
        'pattern' => '1(?:0[1-3]|12)',
        'example' => '101',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0[1-3]|12)|(?:404|880)0',
        'example' => '101',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '(?:404|880)\\d',
        'example' => '4040',
        'posLength' => [
            4,
        ],
    ],
    'smsServices' => [
        'pattern' => '(?:404|880)\\d',
        'example' => '4040',
        'posLength' => [
            4,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
