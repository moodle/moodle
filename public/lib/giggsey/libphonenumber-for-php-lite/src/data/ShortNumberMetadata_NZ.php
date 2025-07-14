<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'NZ',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '\\d{3,4}',
        'posLength' => [
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '111',
        'example' => '111',
        'posLength' => [
            3,
        ],
    ],
    'premiumRate' => [
        'pattern' => '018',
        'example' => '018',
        'posLength' => [
            3,
        ],
    ],
    'emergency' => [
        'pattern' => '111',
        'example' => '111',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '018|1(?:(?:1|37)1|(?:23|94)4|7[03]7)|[2-57-9]\\d{2,3}|6(?:161|26[0-3]|742)',
        'example' => '018',
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
        'pattern' => '018|(?:1(?:23|37|7[03]|94)|6(?:[12]6|74))\\d|[2-57-9]\\d{2,3}',
        'example' => '018',
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
