<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'FI',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[17]\\d\\d(?:\\d{2,3})?',
        'posLength' => [
            3,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '11(?:2|6\\d{3})',
        'example' => '112',
        'posLength' => [
            3,
            6,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '112',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '11(?:2|6(?:00[06]|1(?:1[17]|23)))|(?:1[2-8]\\d|75[12])\\d\\d',
        'example' => '112',
    ],
    'standardRate' => [
        'pattern' => '1[2-8]\\d{3}',
        'example' => '12000',
        'posLength' => [
            5,
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
