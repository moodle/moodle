<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'IQ',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1479]\\d{2,4}',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:0[04]|15|22)',
        'example' => '100',
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
        'pattern' => '1(?:0[04]|15|22)',
        'example' => '100',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0[04]|15|22)|4432|71117|9988',
        'example' => '100',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '(?:443|711\\d|998)\\d',
        'example' => '4430',
        'posLength' => [
            4,
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '(?:443|711\\d|998)\\d',
        'example' => '4430',
        'posLength' => [
            4,
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
