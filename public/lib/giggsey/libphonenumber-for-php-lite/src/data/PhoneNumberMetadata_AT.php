<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'AT',
    'countryCode' => 43,
    'generalDesc' => [
        'pattern' => '1\\d{3,12}|2\\d{6,12}|43(?:(?:0\\d|5[02-9])\\d{3,9}|2\\d{4,5}|[3467]\\d{4}|8\\d{4,6}|9\\d{4,7})|5\\d{4,12}|8\\d{7,12}|9\\d{8,12}|(?:[367]\\d|4[0-24-9])\\d{4,11}',
        'posLength' => [
            4,
            5,
            6,
            7,
            8,
            9,
            10,
            11,
            12,
            13,
        ],
        'posLengthLocal' => [
            3,
        ],
    ],
    'fixedLine' => [
        'pattern' => '1(?:11\\d|[2-9]\\d{3,11})|(?:316|463|(?:51|66|73)2)\\d{3,10}|(?:2(?:1[467]|2[13-8]|5[2357]|6[1-46-8]|7[1-8]|8[124-7]|9[1458])|3(?:1[1-578]|3[23568]|4[5-7]|5[1378]|6[1-38]|8[3-68])|4(?:2[1-8]|35|7[1368]|8[2457])|5(?:2[1-8]|3[357]|4[147]|5[12578]|6[37])|6(?:13|2[1-47]|4[135-8]|5[468])|7(?:2[1-8]|35|4[13478]|5[68]|6[16-8]|7[1-6]|9[45]))\\d{4,10}',
        'example' => '1234567890',
        'posLengthLocal' => [
            3,
        ],
    ],
    'mobile' => [
        'pattern' => '6(?:5[0-3579]|6[013-9]|[7-9]\\d)\\d{4,10}',
        'example' => '664123456',
        'posLength' => [
            7,
            8,
            9,
            10,
            11,
            12,
            13,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{6,10}',
        'example' => '800123456',
        'posLength' => [
            9,
            10,
            11,
            12,
            13,
        ],
    ],
    'premiumRate' => [
        'pattern' => '(?:8[69][2-68]|9(?:0[01]|3[019]))\\d{6,10}',
        'example' => '900123456',
        'posLength' => [
            9,
            10,
            11,
            12,
            13,
        ],
    ],
    'sharedCost' => [
        'pattern' => '8(?:10|2[018])\\d{6,10}|828\\d{5}',
        'example' => '810123456',
        'posLength' => [
            8,
            9,
            10,
            11,
            12,
            13,
        ],
    ],
    'personalNumber' => [
        'posLength' => [
            -1,
        ],
    ],
    'voip' => [
        'pattern' => '5(?:0[1-9]|17|[79]\\d)\\d{2,10}|7[28]0\\d{6,10}',
        'example' => '780123456',
        'posLength' => [
            5,
            6,
            7,
            8,
            9,
            10,
            11,
            12,
            13,
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
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{4})',
            'format' => '$1',
            'leadingDigitsPatterns' => [
                '14',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3,12})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1(?:11|[2-9])',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '517',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3,5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '5[079]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{6})',
            'format' => '$1',
            'leadingDigitsPatterns' => [
                '[18]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3,10})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '(?:31|4)6|51|6(?:5[0-3579]|[6-9])|7(?:20|32|8)|[89]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{3,9})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[2-467]|5[2-6]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '5',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{4,7})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '5',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'intlNumberFormat' => [
        [
            'pattern' => '(\\d)(\\d{3,12})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1(?:11|[2-9])',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '517',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3,5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '5[079]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3,10})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '(?:31|4)6|51|6(?:5[0-3579]|[6-9])|7(?:20|32|8)|[89]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{3,9})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[2-467]|5[2-6]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '5',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{4,7})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '5',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
