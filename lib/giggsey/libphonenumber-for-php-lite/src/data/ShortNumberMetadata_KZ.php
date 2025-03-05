<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'KZ',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1-4]\\d{2,4}',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:0[1-3]|12)|212\\d',
        'example' => '101',
        'posLength' => [
            3,
            4,
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
        'pattern' => '1(?:0[1-4]|12)|2121|(?:3040|404)0',
        'example' => '101',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '(?:304\\d|404)\\d',
        'example' => '4040',
        'posLength' => [
            4,
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '(?:304\\d|404)\\d',
        'example' => '4040',
        'posLength' => [
            4,
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
