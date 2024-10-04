<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SJ',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[01]\\d\\d(?:\\d{2})?',
        'posLength' => [
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '11[023]',
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
        'pattern' => '11[023]',
        'example' => '110',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '04\\d{3}|11[023]',
        'example' => '110',
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
        'pattern' => '04\\d{3}',
        'example' => '04000',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
