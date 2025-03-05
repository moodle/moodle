<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'AL',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[15]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1(?:2|6[01]\\d\\d)|2[7-9]|3[15]|41)',
        'example' => '112',
        'posLength' => [
            3,
            6,
        ],
    ],
    'premiumRate' => [
        'pattern' => '5\\d{4}',
        'example' => '50000',
        'posLength' => [
            5,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:12|2[7-9])',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1(?:6(?:000|1(?:06|11|23))|8\\d\\d)|65\\d|89[12])|5\\d{4}|1(?:[1349]\\d|2[2-9])',
        'example' => '110',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '123',
        'example' => '123',
        'posLength' => [
            3,
        ],
    ],
    'smsServices' => [
        'pattern' => '131|5\\d{4}',
        'example' => '131',
        'posLength' => [
            3,
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
