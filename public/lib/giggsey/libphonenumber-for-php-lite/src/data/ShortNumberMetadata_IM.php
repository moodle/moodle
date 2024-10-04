<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'IM',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[189]\\d\\d(?:\\d{2,3})?',
        'posLength' => [
            3,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '999',
        'example' => '999',
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
        'pattern' => '999',
        'example' => '999',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1\\d\\d(?:\\d{3})?|8(?:6444|9887)|999',
        'example' => '100',
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
        'pattern' => '8(?:64|98)\\d\\d',
        'example' => '86400',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
