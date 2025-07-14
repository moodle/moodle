<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SB',
    'countryCode' => 677,
    'generalDesc' => [
        'pattern' => '[6-9]\\d{6}|[1-6]\\d{4}',
        'posLength' => [
            5,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:1[4-79]|[23]\\d|4[0-2]|5[03]|6[0-37])\\d{3}',
        'example' => '40123',
        'posLength' => [
            5,
        ],
    ],
    'mobile' => [
        'pattern' => '48\\d{3}|(?:(?:6[89]|7[1-9]|8[4-9])\\d|9(?:1[2-9]|2[013-9]|3[0-2]|[46]\\d|5[0-46-9]|7[0-689]|8[0-79]|9[0-8]))\\d{4}',
        'example' => '7421234',
    ],
    'tollFree' => [
        'pattern' => '1[38]\\d{3}',
        'example' => '18123',
        'posLength' => [
            5,
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
        'pattern' => '5[12]\\d{3}',
        'example' => '51123',
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
    'internationalPrefix' => '0[01]',
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '6[89]|7|8[4-9]|9(?:[1-8]|9[0-8])',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
