<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'JP',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[01]\\d\\d(?:\\d{7})?',
        'posLength' => [
            3,
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '11[089]',
        'example' => '110',
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
        'pattern' => '11[09]',
        'example' => '110',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '000[259]\\d{6}|1(?:0[24]|1[089]|44|89)',
        'example' => '102',
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
        'pattern' => '000[259]\\d{6}',
        'example' => '0002000000',
        'posLength' => [
            10,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
