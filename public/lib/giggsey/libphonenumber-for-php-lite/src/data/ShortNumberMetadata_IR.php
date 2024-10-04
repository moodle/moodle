<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'IR',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[129]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1[0-68]|2[0-59]|9[0-579])|911',
        'example' => '110',
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
        'pattern' => '1(?:1[025]|25)|911',
        'example' => '110',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1[0-68]|2[0-59]|3[346-8]|4(?:[0147]|[289]0)|5(?:0[14]|1[02479]|2[0-3]|39|[49]0|65)|6(?:[16]6|[27]|90)|8(?:03|1[18]|22|3[37]|4[28]|88|99)|9[0-579])|20(?:[09]0|1(?:[038]|1[079]|26|9[69])|2[01])|9(?:11|9(?:0009|90))',
        'example' => '110',
    ],
    'standardRate' => [
        'pattern' => '1(?:5[0-469]|8[0-489])\\d',
        'example' => '1500',
        'posLength' => [
            4,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '(?:1(?:5[0-469]|8[0-489])|99(?:0\\d\\d|9))\\d',
        'example' => '1500',
        'posLength' => [
            4,
            6,
        ],
    ],
    'smsServices' => [
        'pattern' => '990\\d{3}',
        'example' => '990000',
        'posLength' => [
            6,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
