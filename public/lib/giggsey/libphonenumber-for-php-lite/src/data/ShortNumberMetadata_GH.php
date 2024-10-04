<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GH',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[14589]\\d{2,4}',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '19[1-3]|999',
        'example' => '191',
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
        'pattern' => '19[1-3]|999',
        'example' => '191',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '19[1-3]|40404|(?:54|83)00|999',
        'example' => '191',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '404\\d\\d|(?:54|83)0\\d',
        'example' => '5400',
        'posLength' => [
            4,
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '404\\d\\d|(?:54|83)0\\d',
        'example' => '5400',
        'posLength' => [
            4,
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
