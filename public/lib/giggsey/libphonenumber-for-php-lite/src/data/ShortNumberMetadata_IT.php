<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'IT',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[14]\\d{2,6}',
        'posLength' => [
            3,
            4,
            5,
            6,
            7,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1(?:[2358]|6\\d{3})|87)',
        'example' => '112',
        'posLength' => [
            3,
            6,
        ],
    ],
    'premiumRate' => [
        'pattern' => '(?:12|4(?:[478](?:[0-4]|[5-9]\\d\\d)|55))\\d\\d',
        'example' => '1200',
        'posLength' => [
            4,
            5,
            7,
        ],
    ],
    'emergency' => [
        'pattern' => '11[2358]',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0\\d{2,3}|1(?:[2-57-9]|6(?:000|111))|3[39]|4(?:82|9\\d{1,3})|5(?:00|1[58]|2[25]|3[03]|44|[59])|60|8[67]|9(?:[01]|2[2-9]|4\\d|696))|4(?:2323|5045)|(?:1(?:2|92[01])|4(?:3(?:[01]|[45]\\d\\d)|[478](?:[0-4]|[5-9]\\d\\d)|55))\\d\\d',
        'example' => '112',
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
        'pattern' => '4(?:3(?:[01]|[45]\\d\\d)|[478](?:[0-4]|[5-9]\\d\\d)|5[05])\\d\\d',
        'example' => '43000',
        'posLength' => [
            5,
            7,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
