<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SE',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1-37-9]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '11(?:2|(?:3|6\\d)\\d\\d|414|77)|900\\d\\d',
        'example' => '112',
    ],
    'premiumRate' => [
        'pattern' => '11811[89]|72\\d{3}',
        'example' => '72000',
        'posLength' => [
            5,
            6,
        ],
    ],
    'emergency' => [
        'pattern' => '112|90000',
        'example' => '112',
        'posLength' => [
            3,
            5,
        ],
    ],
    'shortCode' => [
        'pattern' => '11(?:[25]|313|6(?:00[06]|1(?:1[17]|23))|7[0-8])|2(?:2[02358]|33|4[01]|50|6[1-4])|32[13]|8(?:22|88)|9(?:0(?:00|51)0|12)|(?:11(?:4|8[02-46-9])|7\\d\\d|90[2-4])\\d\\d|(?:118|90)1(?:[02-9]\\d|1[013-9])',
        'example' => '112',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '2(?:2[02358]|33|4[01]|50|6[1-4])|32[13]|8(?:22|88)|912',
        'example' => '220',
        'posLength' => [
            3,
        ],
    ],
    'smsServices' => [
        'pattern' => '7\\d{4}',
        'example' => '70000',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
