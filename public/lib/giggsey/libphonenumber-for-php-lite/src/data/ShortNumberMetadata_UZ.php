<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'UZ',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[04]\\d(?:\\d(?:\\d{2})?)?',
        'posLength' => [
            2,
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '0(?:0[1-3]|[1-3]|50)',
        'example' => '01',
        'posLength' => [
            2,
            3,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '0(?:0[1-3]|[1-3]|50)',
        'example' => '01',
        'posLength' => [
            2,
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '0(?:0[1-3]|[1-3]|50)|45400',
        'example' => '01',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '454\\d\\d',
        'example' => '45400',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '454\\d\\d',
        'example' => '45400',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
