<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BJ',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[17]\\d{2,3}',
        'posLength' => [
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1[246-8]|3[68]|6[06])|7[3-5]\\d\\d',
        'example' => '112',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '11[246-8]',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:05|1[24-8]|2[02-5]|3[126-8]|5[05]|6[06]|89)|7[0-5]\\d\\d',
        'example' => '105',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '12[02-5]',
        'example' => '120',
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
