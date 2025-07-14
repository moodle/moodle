<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'US',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1-9]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '112|611|9(?:11|33|88)',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'premiumRate' => [
        'pattern' => '24280|(?:381|968)35|4(?:3355|7553|8221)|5(?:(?:489|934)2|5928)|72078|(?:323|960)40|(?:276|414)63|(?:2(?:520|744)|7390|9968)9|(?:693|732|976)88|(?:3(?:556|825)|5294|8623|9729)4|(?:3378|4136|7642|8961|9979)6|(?:4(?:6(?:15|32)|827)|(?:591|720)8|9529)7',
        'example' => '24280',
        'posLength' => [
            5,
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
        'pattern' => '11(?:2|5[1-47]|[68]\\d|7[0-57]|98)|[2-9]\\d{3,5}|[2-8]11|9(?:11|33|88)',
        'example' => '112',
    ],
    'standardRate' => [
        'pattern' => '2(?:3333|(?:4224|7562|900)2|56447|6688)|3(?:1010|2665|7404)|40404|560560|6(?:0060|22639|5246|7622)|7(?:0701|3822|4666)|8(?:(?:3825|7226)5|4816)|99099',
        'example' => '23333',
        'posLength' => [
            5,
            6,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '336\\d\\d|[2-9]\\d{3}|[2356]11',
        'example' => '211',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '[2-9]\\d{4,5}',
        'example' => '20000',
        'posLength' => [
            5,
            6,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
