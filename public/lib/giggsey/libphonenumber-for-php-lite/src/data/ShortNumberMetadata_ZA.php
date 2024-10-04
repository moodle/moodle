<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'ZA',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[134]\\d{2,4}',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:01\\d\\d|12)',
        'example' => '112',
        'posLength' => [
            3,
            5,
        ],
    ],
    'premiumRate' => [
        'pattern' => '41(?:348|851)',
        'example' => '41348',
        'posLength' => [
            5,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:01(?:11|77)|12)',
        'example' => '112',
        'posLength' => [
            3,
            5,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0(?:1(?:11|77)|20|7)|1[12]|77(?:3[237]|[45]7|6[279]|9[26]))|[34]\\d{4}',
        'example' => '107',
    ],
    'standardRate' => [
        'pattern' => '3(?:078[23]|7(?:064|567)|8126)|4(?:394[16]|7751|8837)|4[23]699',
        'example' => '30782',
        'posLength' => [
            5,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '111',
        'example' => '111',
        'posLength' => [
            3,
        ],
    ],
    'smsServices' => [
        'pattern' => '[34]\\d{4}',
        'example' => '30000',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
