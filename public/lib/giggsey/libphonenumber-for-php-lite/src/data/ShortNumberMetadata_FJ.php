<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'FJ',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[0-579]\\d(?:\\d(?:\\d{2})?)?',
        'posLength' => [
            2,
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '91[17]',
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
        'pattern' => '91[17]',
        'example' => '911',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '0(?:1[34]|8[1-4])|1(?:0[1-3]|[25]9)|2[289]|30|40404|91[137]|[45]4|75',
        'example' => '22',
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
        'pattern' => '404\\d\\d',
        'example' => '40400',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
