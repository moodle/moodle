<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MQ',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[13]\\d(?:\\d(?:\\d(?:\\d{2})?)?)?',
        'posLength' => [
            2,
            3,
            4,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:12|[578])|3[01]\\d\\d',
        'example' => '15',
        'posLength' => [
            2,
            3,
            4,
        ],
    ],
    'premiumRate' => [
        'pattern' => '3[2469]\\d\\d',
        'example' => '3200',
        'posLength' => [
            4,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:12|[578])',
        'example' => '15',
        'posLength' => [
            2,
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:12|[578])|(?:118[02-9]|3[0-2469])\\d\\d',
        'example' => '15',
    ],
    'standardRate' => [
        'pattern' => '118\\d{3}',
        'example' => '118000',
        'posLength' => [
            6,
        ],
    ],
    'carrierSpecific' => [
        'posLength' => [
            -1,
        ],
    ],
    'smsServices' => [
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
