<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GY',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[019]\\d{2,3}',
        'posLength' => [
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '91[1-3]',
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
        'pattern' => '91[1-3]',
        'example' => '911',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '0(?:02|(?:17|80)1|444|7(?:[67]7|9)|9(?:0[78]|[2-47]))|1(?:443|5[568])|91[1-3]',
        'example' => '002',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '144\\d',
        'example' => '1440',
        'posLength' => [
            4,
        ],
    ],
    'smsServices' => [
        'pattern' => '144\\d',
        'example' => '1440',
        'posLength' => [
            4,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
