<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'RO',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[18]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '11(?:2|6\\d{3})',
        'example' => '112',
        'posLength' => [
            3,
            6,
        ],
    ],
    'premiumRate' => [
        'pattern' => '(?:1(?:18[39]|[24])|8[48])\\d\\d',
        'example' => '1200',
        'posLength' => [
            4,
            6,
        ],
    ],
    'emergency' => [
        'pattern' => '112',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1(?:2|6(?:000|1(?:11|23))|8(?:(?:01|8[18])1|119|[23]00|932))|[24]\\d\\d|9(?:0(?:00|19)|1[19]|21|3[02]|5[178]))|8[48]\\d\\d',
        'example' => '112',
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
        'pattern' => '(?:1[24]|8[48])\\d\\d',
        'example' => '1200',
        'posLength' => [
            4,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
