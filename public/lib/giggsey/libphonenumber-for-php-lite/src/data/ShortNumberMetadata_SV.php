<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SV',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[149]\\d\\d(?:\\d{2,3})?',
        'posLength' => [
            3,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '116\\d{3}|911',
        'example' => '911',
        'posLength' => [
            3,
            6,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '91[13]',
        'example' => '911',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1(?:2|6111)|2[136-8]|3[0-6]|9[05])|40404|9(?:1\\d|29)',
        'example' => '112',
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
