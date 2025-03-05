<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'IS',
    'countryCode' => 354,
    'generalDesc' => [
        'pattern' => '(?:38\\d|[4-9])\\d{6}',
        'posLength' => [
            7,
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:4(?:1[0-24-69]|2[0-7]|[37][0-8]|4[0-24589]|5[0-68]|6\\d|8[0-36-8])|5(?:05|[156]\\d|2[02578]|3[0-579]|4[03-7]|7[0-2578]|8[0-35-9]|9[013-689])|872)\\d{4}',
        'example' => '4101234',
        'posLength' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:38[589]\\d\\d|6(?:1[1-8]|2[0-6]|3[026-9]|4[014679]|5[0159]|6[0-69]|70|8[06-8]|9\\d)|7(?:5[057]|[6-9]\\d)|8(?:2[0-59]|[3-69]\\d|8[238]))\\d{4}',
        'example' => '6111234',
    ],
    'tollFree' => [
        'pattern' => '80[0-8]\\d{4}',
        'example' => '8001234',
        'posLength' => [
            7,
        ],
    ],
    'premiumRate' => [
        'pattern' => '90(?:0\\d|1[5-79]|2[015-79]|3[135-79]|4[125-7]|5[25-79]|7[1-37]|8[0-35-7])\\d{3}',
        'example' => '9001234',
        'posLength' => [
            7,
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
        'pattern' => '49[0-24-79]\\d{4}',
        'example' => '4921234',
        'posLength' => [
            7,
        ],
    ],
    'pager' => [
        'posLength' => [
            -1,
        ],
    ],
    'uan' => [
        'pattern' => '809\\d{4}',
        'example' => '8091234',
        'posLength' => [
            7,
        ],
    ],
    'voicemail' => [
        'pattern' => '(?:689|8(?:7[18]|80)|95[48])\\d{4}',
        'example' => '6891234',
        'posLength' => [
            7,
        ],
    ],
    'noInternationalDialling' => [
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '00|1(?:0(?:01|[12]0)|100)',
    'preferredInternationalPrefix' => '00',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[4-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '3',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
