<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SJ',
    'countryCode' => 47,
    'generalDesc' => [
        'pattern' => '0\\d{4}|(?:[489]\\d|79)\\d{6}',
        'posLength' => [
            5,
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '79\\d{6}',
        'example' => '79123456',
        'posLength' => [
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:4[015-8]|9\\d)\\d{6}',
        'example' => '41234567',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '80[01]\\d{5}',
        'example' => '80012345',
        'posLength' => [
            8,
        ],
    ],
    'premiumRate' => [
        'pattern' => '82[09]\\d{5}',
        'example' => '82012345',
        'posLength' => [
            8,
        ],
    ],
    'sharedCost' => [
        'pattern' => '810(?:0[0-6]|[2-8]\\d)\\d{3}',
        'example' => '81021234',
        'posLength' => [
            8,
        ],
    ],
    'personalNumber' => [
        'pattern' => '880\\d{5}',
        'example' => '88012345',
        'posLength' => [
            8,
        ],
    ],
    'voip' => [
        'pattern' => '85[0-5]\\d{5}',
        'example' => '85012345',
        'posLength' => [
            8,
        ],
    ],
    'pager' => [
        'posLength' => [
            -1,
        ],
    ],
    'uan' => [
        'pattern' => '(?:0[235-9]|81(?:0(?:0[7-9]|1\\d)|5\\d\\d))\\d{3}',
        'example' => '02000',
    ],
    'voicemail' => [
        'pattern' => '81[23]\\d{5}',
        'example' => '81212345',
        'posLength' => [
            8,
        ],
    ],
    'noInternationalDialling' => [
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '00',
    'numberFormat' => [],
    'leadingDigits' => '79',
];
