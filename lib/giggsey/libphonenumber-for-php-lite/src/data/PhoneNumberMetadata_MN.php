<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MN',
    'countryCode' => 976,
    'generalDesc' => [
        'pattern' => '[12]\\d{7,9}|[5-9]\\d{7}',
        'posLength' => [
            8,
            9,
            10,
        ],
        'posLengthLocal' => [
            4,
            5,
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '[12]2[1-3]\\d{5,6}|(?:(?:[12](?:1|27)|5[368])\\d\\d|7(?:0(?:[0-5]\\d|7[078]|80)|128))\\d{4}|[12](?:3[2-8]|4[2-68]|5[1-4689])\\d{6,7}',
        'example' => '53123456',
        'posLengthLocal' => [
            4,
            5,
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:83[01]|92[039])\\d{5}|(?:5[05]|6[069]|72|8[015689]|9[013-9])\\d{6}',
        'example' => '88123456',
        'posLength' => [
            8,
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
        'pattern' => '712[0-79]\\d{4}|7(?:1[013-9]|[5-9]\\d)\\d{5}',
        'example' => '75123456',
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
    'internationalPrefix' => '001',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[12]1',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[5-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{5,6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[12]2[1-3]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{5,6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[12](?:27|3[2-8]|4[2-68]|5[1-4689])',
                '[12](?:27|3[2-8]|4[2-68]|5[1-4689])[0-3]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{5})(\\d{4,5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[12]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
