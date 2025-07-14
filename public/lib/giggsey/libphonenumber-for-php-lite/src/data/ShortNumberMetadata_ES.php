<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'ES',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[0-379]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '0(?:16|6[57]|8[58])|1(?:006|12|[3-7]\\d\\d)|(?:116|20\\d)\\d{3}',
        'example' => '016',
        'posLength' => [
            3,
            4,
            6,
        ],
    ],
    'premiumRate' => [
        'pattern' => '[12]2\\d{1,4}|90(?:5\\d|7)|(?:118|2(?:[357]\\d|80)|3[357]\\d)\\d\\d|[79]9[57]\\d{3}',
        'example' => '120',
    ],
    'emergency' => [
        'pattern' => '08[58]|112',
        'example' => '085',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '0(?:1[0-26]|6[0-257]|8[058]|9[12])|1(?:0[03-57]\\d{1,3}|1(?:2|6(?:000|111)|8\\d\\d)|2\\d{1,4}|[3-9]\\d\\d)|2(?:2\\d{1,4}|80\\d\\d)|90(?:5[124578]|7)|1(?:3[34]|77)|(?:2[01]\\d|[79]9[57])\\d{3}|[23][357]\\d{3}',
        'example' => '010',
    ],
    'standardRate' => [
        'pattern' => '0(?:[16][0-2]|80|9[12])|21\\d{4}',
        'example' => '010',
        'posLength' => [
            3,
            6,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '1(?:3[34]|77)|[12]2\\d{1,4}',
        'example' => '120',
    ],
    'smsServices' => [
        'pattern' => '(?:2[0-2]\\d|3[357]|[79]9[57])\\d{3}|2(?:[2357]\\d|80)\\d\\d',
        'example' => '22000',
        'posLength' => [
            5,
            6,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
