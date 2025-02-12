<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'RU',
    'countryCode' => 7,
    'generalDesc' => [
        'pattern' => '8\\d{13}|[347-9]\\d{9}',
        'posLength' => [
            10,
            14,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:3(?:0[12]|4[1-35-79]|5[1-3]|65|8[1-58]|9[0145])|4(?:01|1[1356]|2[13467]|7[1-5]|8[1-7]|9[1-689])|8(?:1[1-8]|2[01]|3[13-6]|4[0-8]|5[15]|6[1-35-79]|7[1-37-9]))\\d{7}',
        'example' => '3011234567',
        'posLength' => [
            10,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '9\\d{9}',
        'example' => '9123456789',
        'posLength' => [
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '8(?:0[04]|108\\d{3})\\d{7}',
        'example' => '8001234567',
    ],
    'premiumRate' => [
        'pattern' => '80[39]\\d{7}',
        'example' => '8091234567',
        'posLength' => [
            10,
        ],
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'pattern' => '808\\d{7}',
        'example' => '8081234567',
        'posLength' => [
            10,
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
    'internationalPrefix' => '810',
    'preferredInternationalPrefix' => '8~10',
    'nationalPrefix' => '8',
    'nationalPrefixForParsing' => '8',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '[0-79]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '7(?:1[0-8]|2[1-9])',
                '7(?:1(?:[0-356]2|4[29]|7|8[27])|2(?:1[23]|[2-9]2))',
                '7(?:1(?:[0-356]2|4[29]|7|8[27])|2(?:13[03-69]|62[013-9]))|72[1-57-9]2',
            ],
            'nationalPrefixFormattingRule' => '8 ($1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{5})(\\d)(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '7(?:1[0-68]|2[1-9])',
                '7(?:1(?:[06][3-6]|[18]|2[35]|[3-5][3-5])|2(?:[13][3-5]|[24-689]|7[457]))',
                '7(?:1(?:0(?:[356]|4[023])|[18]|2(?:3[013-9]|5)|3[45]|43[013-79]|5(?:3[1-8]|4[1-7]|5)|6(?:3[0-35-9]|[4-6]))|2(?:1(?:3[178]|[45])|[24-689]|3[35]|7[457]))|7(?:14|23)4[0-8]|71(?:33|45)[1-79]',
            ],
            'nationalPrefixFormattingRule' => '8 ($1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '7',
            ],
            'nationalPrefixFormattingRule' => '8 ($1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2-$3-$4',
            'leadingDigitsPatterns' => [
                '[349]|8(?:[02-7]|1[1-8])',
            ],
            'nationalPrefixFormattingRule' => '8 ($1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '8 ($1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'intlNumberFormat' => [
        [
            'pattern' => '(\\d{4})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '7(?:1[0-8]|2[1-9])',
                '7(?:1(?:[0-356]2|4[29]|7|8[27])|2(?:1[23]|[2-9]2))',
                '7(?:1(?:[0-356]2|4[29]|7|8[27])|2(?:13[03-69]|62[013-9]))|72[1-57-9]2',
            ],
            'nationalPrefixFormattingRule' => '8 ($1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{5})(\\d)(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '7(?:1[0-68]|2[1-9])',
                '7(?:1(?:[06][3-6]|[18]|2[35]|[3-5][3-5])|2(?:[13][3-5]|[24-689]|7[457]))',
                '7(?:1(?:0(?:[356]|4[023])|[18]|2(?:3[013-9]|5)|3[45]|43[013-79]|5(?:3[1-8]|4[1-7]|5)|6(?:3[0-35-9]|[4-6]))|2(?:1(?:3[178]|[45])|[24-689]|3[35]|7[457]))|7(?:14|23)4[0-8]|71(?:33|45)[1-79]',
            ],
            'nationalPrefixFormattingRule' => '8 ($1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '7',
            ],
            'nationalPrefixFormattingRule' => '8 ($1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2-$3-$4',
            'leadingDigitsPatterns' => [
                '[349]|8(?:[02-7]|1[1-8])',
            ],
            'nationalPrefixFormattingRule' => '8 ($1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '8 ($1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mainCountryForCode' => true,
    'leadingDigits' => '3[04-689]|[489]',
    'mobileNumberPortableRegion' => true,
];
