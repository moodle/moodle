<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'JO',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[19]\\d\\d(?:\\d{2})?',
        'posLength' => [
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:0[235]|1[2-6]|9[127])|911',
        'example' => '102',
        'posLength' => [
            3,
        ],
    ],
    'premiumRate' => [
        'pattern' => '9[0-4689]\\d{3}',
        'example' => '90000',
        'posLength' => [
            5,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:12|9[127])|911',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0[2359]|1[0-68]|9[0-24-79])|9[0-4689]\\d{3}|911',
        'example' => '102',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '9[0-4689]\\d{3}',
        'example' => '90000',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '9[0-4689]\\d{3}',
        'example' => '90000',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
