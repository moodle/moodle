<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'VN',
    'countryCode' => 84,
    'generalDesc' => [
        'pattern' => '[12]\\d{9}|[135-9]\\d{8}|[16]\\d{7}|[16-8]\\d{6}',
        'posLength' => [
            7,
            8,
            9,
            10,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2(?:0[3-9]|1[0-689]|2[0-25-9]|[38][2-9]|4[2-8]|5[124-9]|6[0-39]|7[0-7]|9[0-4679])\\d{7}',
        'example' => '2101234567',
        'posLength' => [
            10,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:5(?:2[238]|59)|89[6-9]|99[013-9])\\d{6}|(?:3\\d|5[1689]|7[06-9]|8[1-8]|9[0-8])\\d{7}',
        'example' => '912345678',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '1800\\d{4,6}|12(?:0[13]|28)\\d{4}',
        'example' => '1800123456',
        'posLength' => [
            8,
            9,
            10,
        ],
    ],
    'premiumRate' => [
        'pattern' => '1900\\d{4,6}',
        'example' => '1900123456',
        'posLength' => [
            8,
            9,
            10,
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
        'pattern' => '672\\d{6}',
        'example' => '672012345',
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
        'pattern' => '(?:[17]99|80\\d)\\d{4}|69\\d{5,6}',
        'example' => '1992000',
        'posLength' => [
            7,
            8,
        ],
    ],
    'voicemail' => [
        'posLength' => [
            -1,
        ],
    ],
    'noInternationalDialling' => [
        'pattern' => '[17]99\\d{4}|69\\d{5,6}',
        'posLength' => [
            7,
            8,
        ],
    ],
    'internationalPrefix' => '00',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[17]99',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{2})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '80',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{4,5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '69',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{4})(\\d{4,6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '6',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[357-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '2[48]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{4})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '2',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
    ],
    'intlNumberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '80',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{4})(\\d{4,6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '6',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[357-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '2[48]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{4})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '2',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
    ],
];
