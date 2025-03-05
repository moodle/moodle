<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'VA',
    'countryCode' => 39,
    'generalDesc' => [
        'pattern' => '0\\d{5,10}|3[0-8]\\d{7,10}|55\\d{8}|8\\d{5}(?:\\d{2,4})?|(?:1\\d|39)\\d{7,8}',
        'posLength' => [
            6,
            7,
            8,
            9,
            10,
            11,
            12,
        ],
    ],
    'fixedLine' => [
        'pattern' => '06698\\d{1,6}',
        'example' => '0669812345',
        'posLength' => [
            6,
            7,
            8,
            9,
            10,
            11,
        ],
    ],
    'mobile' => [
        'pattern' => '3[1-9]\\d{8}|3[2-9]\\d{7}',
        'example' => '3123456789',
        'posLength' => [
            9,
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '80(?:0\\d{3}|3)\\d{3}',
        'example' => '800123456',
        'posLength' => [
            6,
            9,
        ],
    ],
    'premiumRate' => [
        'pattern' => '(?:0878\\d{3}|89(?:2\\d|3[04]|4(?:[0-4]|[5-9]\\d\\d)|5[0-4]))\\d\\d|(?:1(?:44|6[346])|89(?:38|5[5-9]|9))\\d{6}',
        'example' => '899123456',
        'posLength' => [
            6,
            8,
            9,
            10,
        ],
    ],
    'sharedCost' => [
        'pattern' => '84(?:[08]\\d{3}|[17])\\d{3}',
        'example' => '848123456',
        'posLength' => [
            6,
            9,
        ],
    ],
    'personalNumber' => [
        'pattern' => '1(?:78\\d|99)\\d{6}',
        'example' => '1781234567',
        'posLength' => [
            9,
            10,
        ],
    ],
    'voip' => [
        'pattern' => '55\\d{8}',
        'example' => '5512345678',
        'posLength' => [
            10,
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
        'pattern' => '3[2-8]\\d{9,10}',
        'example' => '33101234501',
        'posLength' => [
            11,
            12,
        ],
    ],
    'noInternationalDialling' => [
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '00',
    'numberFormat' => [],
    'leadingDigits' => '06698',
    'mobileNumberPortableRegion' => true,
];
