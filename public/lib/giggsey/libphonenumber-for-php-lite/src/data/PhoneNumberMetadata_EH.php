<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'EH',
    'countryCode' => 212,
    'generalDesc' => [
        'pattern' => '[5-8]\\d{8}',
        'posLength' => [
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '528[89]\\d{5}',
        'example' => '528812345',
    ],
    'mobile' => [
        'pattern' => '(?:6(?:[0-79]\\d|8[0-247-9])|7(?:[0167]\\d|2[0-467]|5[0-3]|8[0-5]))\\d{6}',
        'example' => '650123456',
    ],
    'tollFree' => [
        'pattern' => '80[0-7]\\d{6}',
        'example' => '801234567',
    ],
    'premiumRate' => [
        'pattern' => '89\\d{7}',
        'example' => '891234567',
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
        'pattern' => '(?:592(?:4[0-2]|93)|80[89]\\d\\d)\\d{4}',
        'example' => '592401234',
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
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [],
    'leadingDigits' => '528[89]',
];
