<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'DZ',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[17]\\d{1,3}',
        'posLength' => [
            2,
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:12|[47]|54\\d)',
        'example' => '14',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:12|[47])',
        'example' => '14',
        'posLength' => [
            2,
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:055|12|[47]|548)|730',
        'example' => '14',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '730',
        'example' => '730',
        'posLength' => [
            3,
        ],
    ],
    'smsServices' => [
        'pattern' => '730',
        'example' => '730',
        'posLength' => [
            3,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
