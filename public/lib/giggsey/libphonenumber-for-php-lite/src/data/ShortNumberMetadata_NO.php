<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'NO',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[01]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '11(?:[023]|6\\d{3})',
        'example' => '110',
        'posLength' => [
            3,
            6,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '11[023]',
        'example' => '110',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '04\\d{3}|1(?:1(?:[0239]|61(?:1[17]|23))|2[048]|4(?:12|[59])|7[57]|8\\d\\d|90)',
        'example' => '110',
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
        'pattern' => '04\\d{3}',
        'example' => '04000',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
