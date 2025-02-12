<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SL',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[069]\\d\\d(?:\\d{2})?',
        'posLength' => [
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '(?:01|99)9',
        'example' => '019',
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
        'pattern' => '(?:01|99)9',
        'example' => '019',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '(?:01|99)9|60400',
        'example' => '019',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '604\\d\\d',
        'example' => '60400',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '604\\d\\d',
        'example' => '60400',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
