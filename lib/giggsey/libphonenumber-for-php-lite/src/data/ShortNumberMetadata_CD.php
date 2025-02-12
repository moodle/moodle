<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CD',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[14]\\d\\d(?:\\d{2})?',
        'posLength' => [
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1[348]|77|88)',
        'example' => '113',
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
        'pattern' => '1(?:1[348]|77|88)',
        'example' => '113',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1[348]|23|77|88)|40404',
        'example' => '113',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '404\\d\\d',
        'example' => '40400',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '404\\d\\d',
        'example' => '40400',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
