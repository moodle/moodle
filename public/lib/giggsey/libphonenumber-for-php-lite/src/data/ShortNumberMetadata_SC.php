<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SC',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[19]\\d{2,3}',
        'posLength' => [
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1[1468]|60)|999',
        'example' => '111',
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
        'pattern' => '999',
        'example' => '999',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:[06]\\d|1[0-246-8]|2[0-8]|3[13]|4[0-2]|5[15]|7[124-6]|8[158]|9[015])|9(?:6\\d\\d|99)',
        'example' => '100',
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
