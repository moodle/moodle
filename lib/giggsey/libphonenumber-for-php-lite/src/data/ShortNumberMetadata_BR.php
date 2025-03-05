<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BR',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1-69]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:00|12|28|8[015]|9[0-47-9])|4(?:57|82\\d)|911',
        'example' => '100',
        'posLength' => [
            3,
            4,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:12|28|9[023])|911',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0(?:[02]|3(?:1[2-579]|2[13-9]|3[124-9]|4[1-3578]|5[1-468]|6[139]|8[149]|9[168])|5[0-35-9]|6(?:0|1[0-35-8]?|2[0145]|3[0137]?|4[37-9]?|5[0-35]|6[016]?|7[137]?|8[5-8]|9[1359]))|1[25-8]|2[357-9]|3[024-68]|4[12568]|5\\d|6[0-8]|8[015]|9[0-47-9])|2(?:7(?:330|878)|85959?)|(?:32|91)1|4(?:0404?|57|828)|55555|6(?:0\\d{4}|10000)|(?:133|411)[12]',
        'example' => '100',
    ],
    'standardRate' => [
        'pattern' => '102|273\\d\\d|321',
        'example' => '102',
        'posLength' => [
            3,
            5,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '151|(?:278|555)\\d\\d|4(?:04\\d\\d?|11\\d|57)',
        'example' => '151',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '285\\d{2,3}|321|40404|(?:27[38]\\d|482)\\d|6(?:0\\d|10)\\d{3}',
        'example' => '321',
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
