<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GT',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[14]\\d{2,4}',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:10|2[03])',
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
        'pattern' => '1(?:10|2[03])',
        'example' => '110',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '110|40404|1(?:2|[57]\\d)\\d',
        'example' => '110',
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
