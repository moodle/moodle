<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CV',
    'countryCode' => 238,
    'generalDesc' => [
        'pattern' => '(?:[2-59]\\d\\d|800)\\d{4}',
        'posLength' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2(?:2[1-7]|3[0-8]|4[12]|5[1256]|6\\d|7[1-3]|8[1-5])\\d{4}',
        'example' => '2211234',
    ],
    'mobile' => [
        'pattern' => '(?:36|5[1-389]|9\\d)\\d{5}',
        'example' => '9911234',
    ],
    'tollFree' => [
        'pattern' => '800\\d{4}',
        'example' => '8001234',
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
        'pattern' => '(?:3[3-5]|4[356])\\d{5}',
        'example' => '3401234',
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
    'internationalPrefix' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[2-589]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
