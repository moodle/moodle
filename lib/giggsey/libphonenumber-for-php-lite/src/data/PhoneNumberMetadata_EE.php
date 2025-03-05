<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'EE',
    'countryCode' => 372,
    'generalDesc' => [
        'pattern' => '8\\d{9}|[4578]\\d{7}|(?:[3-8]\\d|90)\\d{5}',
        'posLength' => [
            7,
            8,
            10,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:3[23589]|4[3-8]|6\\d|7[1-9]|88)\\d{5}',
        'example' => '3212345',
        'posLength' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:5\\d{5}|8(?:1(?:0(?:0(?:00|[178]\\d)|[3-9]\\d\\d)|(?:1(?:0[2-6]|1\\d)|(?:2[0-59]|[3-79]\\d)\\d)\\d)|2(?:0(?:0(?:00|4\\d)|(?:19|[2-7]\\d)\\d)|(?:(?:[124-69]\\d|3[5-9])\\d|7(?:[0-79]\\d|8[13-9])|8(?:[2-6]\\d|7[01]))\\d)|[349]\\d{4}))\\d\\d|5(?:(?:[02]\\d|5[0-478])\\d|1(?:[0-8]\\d|95)|6(?:4[0-4]|5[1-589]))\\d{3}',
        'example' => '51234567',
        'posLength' => [
            7,
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '800(?:(?:0\\d\\d|1)\\d|[2-9])\\d{3}',
        'example' => '80012345',
    ],
    'premiumRate' => [
        'pattern' => '(?:40\\d\\d|900)\\d{4}',
        'example' => '9001234',
        'posLength' => [
            7,
            8,
        ],
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'pattern' => '70[0-2]\\d{5}',
        'example' => '70012345',
        'posLength' => [
            8,
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
        'pattern' => '800[2-9]\\d{3}',
        'posLength' => [
            7,
        ],
    ],
    'internationalPrefix' => '00',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[369]|4[3-8]|5(?:[0-2]|5[0-478]|6[45])|7[1-9]|88',
                '[369]|4[3-8]|5(?:[02]|1(?:[0-8]|95)|5[0-478]|6(?:4[0-4]|5[1-589]))|7[1-9]|88',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{3,4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[45]|8(?:00|[1-49])',
                '[45]|8(?:00[1-9]|[1-49])',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '7',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{3})(\\d{3})',
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
