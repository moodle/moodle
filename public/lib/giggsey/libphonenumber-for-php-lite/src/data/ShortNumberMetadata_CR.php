<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CR',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1359]\\d{2,3}',
        'posLength' => [
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '112|911',
        'example' => '112',
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
        'pattern' => '112|911',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0(?:00|15|2[2-4679])|1(?:1[0-35-9]|2|37|[46]6|7[57]|8[79]|9[0-379])|2(?:00|[12]2|34|55)|3(?:21|33)|4(?:0[06]|1[4-6])|5(?:15|5[15])|693|7(?:00|1[7-9]|2[02]|[67]7)|975)|3855|5(?:0(?:30|49)|510)|911',
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
        'pattern' => '(?:385|5(?:0[34]|51))\\d',
        'example' => '3850',
        'posLength' => [
            4,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
