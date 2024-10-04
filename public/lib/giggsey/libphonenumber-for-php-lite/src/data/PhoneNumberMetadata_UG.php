<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'UG',
    'countryCode' => 256,
    'generalDesc' => [
        'pattern' => '800\\d{6}|(?:[29]0|[347]\\d)\\d{7}',
        'posLength' => [
            9,
        ],
        'posLengthLocal' => [
            5,
            6,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '20(?:(?:240|30[67])\\d|6(?:00[0-2]|30[0-4]))\\d{3}|(?:20(?:[017]\\d|2[5-9]|3[1-4]|5[0-4]|6[15-9])|[34]\\d{3})\\d{5}',
        'example' => '312345678',
        'posLengthLocal' => [
            5,
            6,
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '72[48]0\\d{5}|7(?:[015-8]\\d|2[067]|36|4[0-7]|9[89])\\d{6}',
        'example' => '712345678',
    ],
    'tollFree' => [
        'pattern' => '800[1-3]\\d{5}',
        'example' => '800123456',
    ],
    'premiumRate' => [
        'pattern' => '90[1-3]\\d{6}',
        'example' => '901123456',
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
        'posLength' => [
            -1,
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
    'internationalPrefix' => '00[057]',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{4})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '202',
                '2024',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[27-9]|4(?:6[45]|[7-9])',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[34]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
