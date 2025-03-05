<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CZ',
    'countryCode' => 420,
    'generalDesc' => [
        'pattern' => '(?:[2-578]\\d|60)\\d{7}|9\\d{8,11}',
        'posLength' => [
            9,
            10,
            11,
            12,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2\\d|3[1257-9]|4[16-9]|5[13-9])\\d{7}',
        'example' => '212345678',
        'posLength' => [
            9,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:60[1-8]\\d|7(?:0(?:[2-5]\\d|60)|19[0-2]|[2379]\\d\\d))\\d{5}',
        'example' => '601123456',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{6}',
        'example' => '800123456',
        'posLength' => [
            9,
        ],
    ],
    'premiumRate' => [
        'pattern' => '9(?:0[05689]|76)\\d{6}',
        'example' => '900123456',
        'posLength' => [
            9,
        ],
    ],
    'sharedCost' => [
        'pattern' => '8[134]\\d{7}',
        'example' => '811234567',
        'posLength' => [
            9,
        ],
    ],
    'personalNumber' => [
        'pattern' => '70[01]\\d{6}',
        'example' => '700123456',
        'posLength' => [
            9,
        ],
    ],
    'voip' => [
        'pattern' => '9[17]0\\d{6}',
        'example' => '910123456',
        'posLength' => [
            9,
        ],
    ],
    'pager' => [
        'posLength' => [
            -1,
        ],
    ],
    'uan' => [
        'pattern' => '9(?:5\\d|7[2-4])\\d{6}',
        'example' => '972123456',
        'posLength' => [
            9,
        ],
    ],
    'voicemail' => [
        'pattern' => '9(?:3\\d{9}|6\\d{7,10})',
        'example' => '93123456789',
    ],
    'noInternationalDialling' => [
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '00',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[2-8]|9[015-7]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '96',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '9',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '9',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
