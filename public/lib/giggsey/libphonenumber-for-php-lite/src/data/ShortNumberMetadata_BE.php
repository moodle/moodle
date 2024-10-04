<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BE',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1-9]\\d\\d(?:\\d(?:\\d{2})?)?',
        'posLength' => [
            3,
            4,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:0[0-35-8]|1[0269]|7(?:12|77)|813)|(?:116|8)\\d{3}',
        'example' => '100',
    ],
    'premiumRate' => [
        'pattern' => '1(?:2[03]|40)4|(?:1(?:[24]1|3[01])|[2-79]\\d\\d)\\d',
        'example' => '1204',
        'posLength' => [
            4,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:0[01]|12)',
        'example' => '100',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0[0-8]|16117|2(?:12|3[0-24])|313|414|5(?:1[05]|5[15]|66|95)|6(?:1[167]|36|6[16])|7(?:[07][017]|1[27-9]|22|33|65)|81[39])|[2-9]\\d{3}|11[02679]|1(?:1600|45)0|1(?:[2-4]9|78)9|1[2-4]0[47]',
        'example' => '100',
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
        'pattern' => '[2-9]\\d{3}',
        'example' => '2000',
        'posLength' => [
            4,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
