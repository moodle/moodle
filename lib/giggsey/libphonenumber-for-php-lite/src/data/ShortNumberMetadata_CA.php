<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CA',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1-9]\\d\\d(?:\\d{2,3})?',
        'posLength' => [
            3,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '112|988|[29]11',
        'example' => '112',
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
        'pattern' => '112|911',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '[1-35-9]\\d{4,5}|112|[2-8]11|9(?:11|88)',
        'example' => '112',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '[235-7]11',
        'example' => '211',
        'posLength' => [
            3,
        ],
    ],
    'smsServices' => [
        'pattern' => '[1-35-9]\\d{4,5}',
        'example' => '10000',
        'posLength' => [
            5,
            6,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
