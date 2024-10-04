<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GI',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[158]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:00|1[25]|23|4(?:1|7\\d)|5[15]|9[02-49])|555|(?:116\\d|80)\\d\\d',
        'example' => '100',
        'posLength' => [
            3,
            4,
            6,
        ],
    ],
    'premiumRate' => [
        'pattern' => '8[1-69]\\d\\d',
        'example' => '8100',
        'posLength' => [
            4,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:12|9[09])',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:00|1(?:[25]|6(?:00[06]|1(?:1[17]|23))|8\\d\\d)|23|4(?:1|7[014])|5[015]|9[02-49])|555|8[0-79]\\d\\d|8(?:00|4[0-2]|8[0-589])',
        'example' => '100',
    ],
    'standardRate' => [
        'pattern' => '150|87\\d\\d',
        'example' => '150',
        'posLength' => [
            3,
            4,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '1(?:00|1(?:5|8\\d\\d)|23|51|9[2-4])|555|8(?:00|4[0-2]|8[0-589])',
        'example' => '100',
        'posLength' => [
            3,
            5,
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
