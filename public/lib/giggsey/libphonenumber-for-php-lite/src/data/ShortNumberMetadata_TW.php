<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'TW',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '1\\d{2,3}',
        'posLength' => [
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '11[0289]|1(?:81|92)\\d',
        'example' => '110',
    ],
    'premiumRate' => [
        'pattern' => '10[56]',
        'example' => '105',
        'posLength' => [
            3,
        ],
    ],
    'emergency' => [
        'pattern' => '11[029]',
        'example' => '110',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0[04-6]|1[0237-9]|3[389]|6[05-8]|7[07]|8(?:0|11)|9(?:19|22|5[057]|68|8[05]|9[15689]))',
        'example' => '100',
    ],
    'standardRate' => [
        'pattern' => '1(?:65|9(?:1\\d|50|85|98))',
        'example' => '165',
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
