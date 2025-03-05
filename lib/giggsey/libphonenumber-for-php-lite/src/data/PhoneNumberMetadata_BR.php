<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BR',
    'countryCode' => 55,
    'generalDesc' => [
        'pattern' => '(?:[1-46-9]\\d\\d|5(?:[0-46-9]\\d|5[0-46-9]))\\d{8}|[1-9]\\d{9}|[3589]\\d{8}|[34]\\d{7}',
        'posLength' => [
            8,
            9,
            10,
            11,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:[14689][1-9]|2[12478]|3[1-578]|5[13-5]|7[13-579])[2-5]\\d{7}',
        'example' => '1123456789',
        'posLength' => [
            10,
        ],
        'posLengthLocal' => [
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:[14689][1-9]|2[12478]|3[1-578]|5[13-5]|7[13-579])(?:7|9\\d)\\d{7}',
        'example' => '11961234567',
        'posLength' => [
            10,
            11,
        ],
        'posLengthLocal' => [
            8,
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{6,7}',
        'example' => '800123456',
        'posLength' => [
            9,
            10,
        ],
    ],
    'premiumRate' => [
        'pattern' => '300\\d{6}|[59]00\\d{6,7}',
        'example' => '300123456',
        'posLength' => [
            9,
            10,
        ],
    ],
    'sharedCost' => [
        'pattern' => '(?:30[03]\\d{3}|4(?:0(?:0\\d|20)|370))\\d{4}|300\\d{5}',
        'example' => '40041234',
        'posLength' => [
            8,
            10,
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
        'pattern' => '30(?:0\\d{5,7}|3\\d{7})|40(?:0\\d|20)\\d{4}|800\\d{6,7}',
        'posLength' => [
            8,
            9,
            10,
        ],
    ],
    'internationalPrefix' => '00(?:1[245]|2[1-35]|31|4[13]|[56]5|99)',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '(?:0|90)(?:(1[245]|2[1-35]|31|4[13]|[56]5|99)(\\d{10,11}))?',
    'nationalPrefixTransformRule' => '$2',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3,6})',
            'format' => '$1',
            'leadingDigitsPatterns' => [
                '1(?:1[25-8]|2[357-9]|3[02-68]|4[12568]|5|6[0-8]|8[015]|9[0-47-9])|321|610',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '300|4(?:0[02]|37)',
                '4(?:02|37)0|[34]00',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '[2-57]',
                '[2357]|4(?:[0-24-9]|3(?:[0-689]|7[1-9]))',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2,3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '(?:[358]|90)0',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{5})(\\d{4})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '9',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{4})',
            'format' => '$1 $2-$3',
            'leadingDigitsPatterns' => [
                '(?:[14689][1-9]|2[12478]|3[1-578]|5[13-5]|7[13-579])[2-57]',
            ],
            'nationalPrefixFormattingRule' => '($1)',
            'domesticCarrierCodeFormattingRule' => '0 $CC ($1)',
        ],
        [
            'pattern' => '(\\d{2})(\\d{5})(\\d{4})',
            'format' => '$1 $2-$3',
            'leadingDigitsPatterns' => [
                '[16][1-9]|[2-57-9]',
            ],
            'nationalPrefixFormattingRule' => '($1)',
            'domesticCarrierCodeFormattingRule' => '0 $CC ($1)',
        ],
    ],
    'intlNumberFormat' => [
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '300|4(?:0[02]|37)',
                '4(?:02|37)0|[34]00',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2,3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '(?:[358]|90)0',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{4})',
            'format' => '$1 $2-$3',
            'leadingDigitsPatterns' => [
                '(?:[14689][1-9]|2[12478]|3[1-578]|5[13-5]|7[13-579])[2-57]',
            ],
            'nationalPrefixFormattingRule' => '($1)',
            'domesticCarrierCodeFormattingRule' => '0 $CC ($1)',
        ],
        [
            'pattern' => '(\\d{2})(\\d{5})(\\d{4})',
            'format' => '$1 $2-$3',
            'leadingDigitsPatterns' => [
                '[16][1-9]|[2-57-9]',
            ],
            'nationalPrefixFormattingRule' => '($1)',
            'domesticCarrierCodeFormattingRule' => '0 $CC ($1)',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
