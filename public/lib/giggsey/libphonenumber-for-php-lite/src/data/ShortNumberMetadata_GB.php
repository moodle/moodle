<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GB',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1-46-9]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:05|1(?:[29]|6\\d{3})|7[56]\\d|8000)|2(?:20\\d|48)|4444|999',
        'example' => '105',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '112|999',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0[015]|1(?:[129]|6(?:000|1(?:11|23))|8\\d{3})|2(?:[1-3]|50)|33|4(?:1|7\\d)|571|7(?:0\\d|[56]0)|800\\d|9[15])|2(?:0202|1300|2(?:02|11)|3(?:02|336|45)|4(?:25|8))|3[13]3|4(?:0[02]|35[01]|44[45]|5\\d)|(?:[68]\\d|7[089])\\d{3}|15\\d|2[02]2|650|789|9(?:01|99)',
        'example' => '100',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '1(?:(?:25|7[56])\\d|571)|2(?:02(?:\\d{2})?|[13]3\\d\\d|48)|4444|901',
        'example' => '202',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '(?:125|2(?:020|13\\d)|(?:7[089]|8[01])\\d\\d)\\d',
        'example' => '1250',
        'posLength' => [
            4,
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
