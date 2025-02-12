<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BW',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[19]\\d\\d(?:\\d{2})?',
        'posLength' => [
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '9(?:11|9[7-9])',
        'example' => '911',
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
        'pattern' => '9(?:11|9[7-9])',
        'example' => '911',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1[26]|3123)|9(?:1[14]|9[1-57-9])',
        'example' => '112',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '131\\d\\d',
        'example' => '13100',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '131\\d\\d',
        'example' => '13100',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
