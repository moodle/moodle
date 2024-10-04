<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'HK',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[19]\\d{2,6}',
        'posLength' => [
            3,
            4,
            5,
            6,
            7,
        ],
    ],
    'tollFree' => [
        'pattern' => '112|99[29]',
        'example' => '112',
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
        'pattern' => '112|99[29]',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0(?:(?:[0136]\\d|2[14])\\d{0,3}|8[138])|12|2(?:[0-3]\\d{0,4}|(?:58|8[13])\\d{0,3})|7(?:[135-9]\\d{0,4}|219\\d{0,2})|8(?:0(?:(?:[13]|60\\d)\\d|8)|1(?:0\\d|[2-8])|2(?:0[5-9]|(?:18|2)2|3|8[128])|(?:(?:3[0-689]\\d|7(?:2[1-389]|8[0235-9]|93))\\d|8)\\d|50[138]|6(?:1(?:11|86)|8)))|99[29]|10[0139]',
        'example' => '100',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '109|1(?:08|85\\d)\\d',
        'example' => '109',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '992',
        'example' => '992',
        'posLength' => [
            3,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
