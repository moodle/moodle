<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'WS',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[19]\\d\\d',
        'posLength' => [
            3,
        ],
    ],
    'tollFree' => [
        'pattern' => '9(?:11|9[4-69])',
        'example' => '911',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '9(?:11|9[4-69])',
        'example' => '911',
    ],
    'shortCode' => [
        'pattern' => '1(?:1[12]|2[0-6]|[39]0)|9(?:11|9[4-79])',
        'example' => '111',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '12[0-6]',
        'example' => '120',
    ],
    'smsServices' => [
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
