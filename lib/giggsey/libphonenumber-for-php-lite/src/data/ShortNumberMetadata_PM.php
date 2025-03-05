<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'PM',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[13]\\d(?:\\d\\d(?:\\d{2})?)?',
        'posLength' => [
            2,
            4,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1[578]|3(?:0\\d|1[689])\\d',
        'example' => '15',
        'posLength' => [
            2,
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
        'pattern' => '1[578]',
        'example' => '15',
        'posLength' => [
            2,
        ],
    ],
    'shortCode' => [
        'pattern' => '1[578]|31(?:03|[689]\\d)|(?:118[02-9]|3[02469])\\d\\d',
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
        'pattern' => '310\\d',
        'example' => '3100',
        'posLength' => [
            4,
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
