<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GE',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[014]\\d\\d(?:\\d{2})?',
        'posLength' => [
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '0(?:11|33)|11[1-3]|[01]22',
        'example' => '011',
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
        'pattern' => '0(?:11|33)|11[1-3]|[01]22',
        'example' => '011',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '0(?:11|33)|11[1-3]|40404|[01]22',
        'example' => '011',
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
