<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SH',
    'countryCode' => 290,
    'generalDesc' => [
        'pattern' => '(?:[256]\\d|8)\\d{3}',
        'posLength' => [
            4,
            5,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2(?:[0-57-9]\\d|6[4-9])\\d\\d',
        'example' => '22158',
    ],
    'mobile' => [
        'pattern' => '[56]\\d{4}',
        'example' => '51234',
        'posLength' => [
            5,
        ],
    ],
    'tollFree' => [
        'posLength' => [
            -1,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'posLength' => [
            -1,
        ],
    ],
    'voip' => [
        'pattern' => '262\\d\\d',
        'example' => '26212',
        'posLength' => [
            5,
        ],
    ],
    'pager' => [
        'posLength' => [
            -1,
        ],
    ],
    'uan' => [
        'posLength' => [
            -1,
        ],
    ],
    'voicemail' => [
        'posLength' => [
            -1,
        ],
    ],
    'noInternationalDialling' => [
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '00',
    'numberFormat' => [],
    'mainCountryForCode' => true,
    'leadingDigits' => '[256]',
];
