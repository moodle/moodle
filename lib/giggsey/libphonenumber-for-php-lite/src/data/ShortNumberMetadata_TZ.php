<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'TZ',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[149]\\d\\d(?:\\d{2})?',
        'posLength' => [
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1[0-79]|9[09])|999',
        'example' => '110',
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
        'pattern' => '11[0-245]|999',
        'example' => '110',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1\\d|9[09])|46400|999',
        'example' => '110',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '464\\d\\d',
        'example' => '46400',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '464\\d\\d',
        'example' => '46400',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
