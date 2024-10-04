<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'NE',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1-3578]\\d(?:\\d(?:\\d{3})?)?',
        'posLength' => [
            2,
            3,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:18|[578])|723\\d{3}',
        'example' => '15',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:18|[578])|723141',
        'example' => '15',
    ],
    'shortCode' => [
        'pattern' => '1(?:0[01]|1[128]|2[034]|3[013]|[46]0|55?|[78])|222|333|555|723141|888',
        'example' => '15',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '1(?:0[01]|1[12]|2[034]|3[013]|[46]0|55)|222|333|555|888',
        'example' => '100',
        'posLength' => [
            3,
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
