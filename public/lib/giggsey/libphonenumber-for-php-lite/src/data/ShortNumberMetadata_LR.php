<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'LR',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[3489]\\d{2,3}',
        'posLength' => [
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '355|911',
        'example' => '355',
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
        'pattern' => '355|911',
        'example' => '355',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '355|4040|8(?:400|933)|911',
        'example' => '355',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '(?:404|8(?:40|93))\\d',
        'example' => '4040',
        'posLength' => [
            4,
        ],
    ],
    'smsServices' => [
        'pattern' => '(?:404|8(?:40|93))\\d',
        'example' => '4040',
        'posLength' => [
            4,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
