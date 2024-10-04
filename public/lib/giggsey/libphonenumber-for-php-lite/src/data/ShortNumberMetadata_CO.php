<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CO',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1-589]\\d\\d(?:\\d{2,3})?',
        'posLength' => [
            3,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1[29]|23|32|56)',
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
        'pattern' => '1(?:1[29]|23|32|56)',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:06|1[2-9]|2[35-7]|3[27]|4[467]|5[36]|6[4-7]|95)|(?:29002|39003)9|40404|5930\\d\\d|85432|(?:[2359][57]|8(?:7|9\\d))\\d{3}',
        'example' => '106',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '(?:40|85)4\\d\\d',
        'example' => '40400',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '(?:40|85)4\\d\\d',
        'example' => '40400',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
