<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'LU',
    'countryCode' => 352,
    'generalDesc' => [
        'pattern' => '35[013-9]\\d{4,8}|6\\d{8}|35\\d{2,4}|(?:[2457-9]\\d|3[0-46-9])\\d{2,9}',
        'posLength' => [
            4,
            5,
            6,
            7,
            8,
            9,
            10,
            11,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:35[013-9]|80[2-9]|90[89])\\d{1,8}|(?:2[2-9]|3[0-46-9]|[457]\\d|8[13-9]|9[2-579])\\d{2,9}',
        'example' => '27123456',
    ],
    'mobile' => [
        'pattern' => '6(?:[269][18]|5[1568]|7[189]|81)\\d{6}',
        'example' => '628123456',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{5}',
        'example' => '80012345',
        'posLength' => [
            8,
        ],
    ],
    'premiumRate' => [
        'pattern' => '90[015]\\d{5}',
        'example' => '90012345',
        'posLength' => [
            8,
        ],
    ],
    'sharedCost' => [
        'pattern' => '801\\d{5}',
        'example' => '80112345',
        'posLength' => [
            8,
        ],
    ],
    'personalNumber' => [
        'posLength' => [
            -1,
        ],
    ],
    'voip' => [
        'pattern' => '20(?:1\\d{5}|[2-689]\\d{1,7})',
        'example' => '20201234',
        'posLength' => [
            4,
            5,
            6,
            7,
            8,
            9,
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
    'nationalPrefixForParsing' => '(15(?:0[06]|1[12]|[35]5|4[04]|6[26]|77|88|99)\\d)',
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{3})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '2(?:0[2-689]|[2-9])|[3-57]|8(?:0[2-9]|[13-9])|9(?:0[89]|[2-579])',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '2(?:0[2-689]|[2-9])|[3-57]|8(?:0[2-9]|[13-9])|9(?:0[89]|[2-579])',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '20[2-689]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{1,2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '2(?:[0367]|4[3-8])',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '80[01]|90[015]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{3})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '20',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '6',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{2})(\\d{1,2})',
            'format' => '$1 $2 $3 $4 $5',
            'leadingDigitsPatterns' => [
                '2(?:[0367]|4[3-8])',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{1,5})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[3-57]|8[13-9]|9(?:0[89]|[2-579])|(?:2|80)[2-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
