<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'ML',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[136-8]\\d{1,4}',
        'posLength' => [
            2,
            3,
            4,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '1[578]|(?:352|67)00|7402|(?:677|744|8000)\\d',
        'example' => '15',
        'posLength' => [
            2,
            4,
            5,
        ],
    ],
    'premiumRate' => [
        'pattern' => '(?:12|800)2\\d|3(?:52(?:11|2[02]|3[04-6]|99)|7574)',
        'example' => '1220',
        'posLength' => [
            4,
            5,
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
        'pattern' => '1(?:1(?:[013-9]\\d|2)|2(?:1[02-469]|2[13])|[578])|350(?:35|57)|67(?:0[09]|[59]9|77|8[89])|74(?:0[02]|44|55)|800[0-2][12]|3(?:52|[67]\\d)\\d\\d',
        'example' => '15',
    ],
    'standardRate' => [
        'pattern' => '37(?:433|575)|7400|8001\\d',
        'example' => '7400',
        'posLength' => [
            4,
            5,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '3503\\d|(?:3[67]\\d|800)\\d\\d',
        'example' => '35030',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '374(?:0[24-9]|[1-9]\\d)|7400|3(?:6\\d|75)\\d\\d',
        'example' => '7400',
        'posLength' => [
            4,
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
