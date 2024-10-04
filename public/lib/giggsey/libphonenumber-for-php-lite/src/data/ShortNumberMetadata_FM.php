<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'FM',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[39]\\d\\d(?:\\d{3})?',
        'posLength' => [
            3,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '320\\d{3}|911',
        'example' => '911',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '(?:32022|91)1',
        'example' => '911',
    ],
    'shortCode' => [
        'pattern' => '(?:32022|91)1',
        'example' => '911',
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
