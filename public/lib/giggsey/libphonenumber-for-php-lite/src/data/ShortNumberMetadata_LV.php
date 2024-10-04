<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'LV',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[018]\\d{1,5}',
        'posLength' => [
            2,
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '0[1-3]|11(?:[023]|6\\d{3})',
        'example' => '01',
        'posLength' => [
            2,
            3,
            6,
        ],
    ],
    'premiumRate' => [
        'pattern' => '1180|821\\d\\d',
        'example' => '1180',
        'posLength' => [
            4,
            5,
        ],
    ],
    'emergency' => [
        'pattern' => '0[1-3]|11[023]',
        'example' => '01',
        'posLength' => [
            2,
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '0[1-4]|1(?:1(?:[02-4]|6(?:000|111)|8[0189])|(?:5|65)5|77)|821[57]4',
        'example' => '01',
    ],
    'standardRate' => [
        'pattern' => '1181',
        'example' => '1181',
        'posLength' => [
            4,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '165\\d',
        'example' => '1650',
        'posLength' => [
            4,
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
