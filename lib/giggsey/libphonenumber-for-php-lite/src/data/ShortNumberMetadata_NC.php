<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'NC',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[135]\\d{1,3}',
        'posLength' => [
            2,
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:0(?:00|1[23]|3[0-2]|8\\d)|[5-8])|363\\d|577',
        'example' => '15',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '1[5-8]',
        'example' => '15',
        'posLength' => [
            2,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0(?:0[06]|1[02-46]|20|3[0-25]|42|5[058]|77|88)|[5-8])|3631|5[6-8]\\d',
        'example' => '15',
    ],
    'standardRate' => [
        'pattern' => '5(?:67|88)',
        'example' => '567',
        'posLength' => [
            3,
        ],
    ],
    'carrierSpecific' => [
        'posLength' => [
            -1,
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
