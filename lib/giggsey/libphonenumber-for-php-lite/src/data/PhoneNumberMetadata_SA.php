<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SA',
    'countryCode' => 966,
    'generalDesc' => [
        'pattern' => '92\\d{7}|(?:[15]|8\\d)\\d{8}',
        'posLength' => [
            9,
            10,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '1(?:1\\d|2[24-8]|3[35-8]|4[3-68]|6[2-5]|7[235-7])\\d{6}',
        'example' => '112345678',
        'posLength' => [
            9,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '579[01]\\d{5}|5(?:[013-689]\\d|7[0-8])\\d{6}',
        'example' => '512345678',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{7}',
        'example' => '8001234567',
        'posLength' => [
            10,
        ],
    ],
    'premiumRate' => [
        'pattern' => '925\\d{6}',
        'example' => '925012345',
        'posLength' => [
            9,
        ],
    ],
    'sharedCost' => [
        'pattern' => '920\\d{6}',
        'example' => '920012345',
        'posLength' => [
            9,
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
        'pattern' => '811\\d{7}',
        'example' => '8110123456',
        'posLength' => [
            10,
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
            'pattern' => '(\\d{4})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '9',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '5',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '81',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
