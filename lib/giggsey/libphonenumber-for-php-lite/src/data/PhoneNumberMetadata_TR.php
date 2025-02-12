<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'TR',
    'countryCode' => 90,
    'generalDesc' => [
        'pattern' => '4\\d{6}|8\\d{11,12}|(?:[2-58]\\d\\d|900)\\d{7}',
        'posLength' => [
            7,
            10,
            12,
            13,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2(?:[13][26]|[28][2468]|[45][268]|[67][246])|3(?:[13][28]|[24-6][2468]|[78][02468]|92)|4(?:[16][246]|[23578][2468]|4[26]))\\d{7}',
        'example' => '2123456789',
        'posLength' => [
            10,
        ],
    ],
    'mobile' => [
        'pattern' => '561(?:011|61\\d)\\d{4}|5(?:0[15-7]|1[06]|24|[34]\\d|5[1-59]|9[46])\\d{7}',
        'example' => '5012345678',
        'posLength' => [
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '8(?:00\\d{7}(?:\\d{2,3})?|11\\d{7})',
        'example' => '8001234567',
        'posLength' => [
            10,
            12,
            13,
        ],
    ],
    'premiumRate' => [
        'pattern' => '(?:8[89]8|900)\\d{7}',
        'example' => '9001234567',
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
        'pattern' => '592(?:21[12]|461)\\d{4}',
        'example' => '5922121234',
        'posLength' => [
            10,
        ],
    ],
    'voip' => [
        'pattern' => '850\\d{7}',
        'example' => '8500123456',
        'posLength' => [
            10,
        ],
    ],
    'pager' => [
        'pattern' => '512\\d{7}',
        'example' => '5123456789',
        'posLength' => [
            10,
        ],
    ],
    'uan' => [
        'pattern' => '444\\d{4}',
        'example' => '4441444',
        'posLength' => [
            7,
        ],
    ],
    'voicemail' => [
        'posLength' => [
            -1,
        ],
    ],
    'noInternationalDialling' => [
        'pattern' => '(?:444|811\\d{3})\\d{4}',
        'posLength' => [
            7,
            10,
        ],
    ],
    'internationalPrefix' => '00',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d)(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '444',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '512|8[01589]|90',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '5(?:[0-59]|61)',
                '5(?:[0-59]|61[06])',
                '5(?:[0-59]|61[06]1)',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[24][1-8]|3[1-9]',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{6,7})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '80',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
    ],
    'intlNumberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '512|8[01589]|90',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '5(?:[0-59]|61)',
                '5(?:[0-59]|61[06])',
                '5(?:[0-59]|61[06]1)',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[24][1-8]|3[1-9]',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{6,7})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '80',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
