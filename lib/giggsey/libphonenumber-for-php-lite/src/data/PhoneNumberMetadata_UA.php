<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'UA',
    'countryCode' => 380,
    'generalDesc' => [
        'pattern' => '[89]\\d{9}|[3-9]\\d{8}',
        'posLength' => [
            9,
            10,
        ],
        'posLengthLocal' => [
            5,
            6,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:3[1-8]|4[13-8]|5[1-7]|6[12459])\\d{7}',
        'example' => '311234567',
        'posLength' => [
            9,
        ],
        'posLengthLocal' => [
            5,
            6,
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '790\\d{6}|(?:39|50|6[36-8]|7[1-357]|9[1-9])\\d{7}',
        'example' => '501234567',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '800[1-8]\\d{5,6}',
        'example' => '800123456',
    ],
    'premiumRate' => [
        'pattern' => '900[239]\\d{5,6}',
        'example' => '900212345',
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
        'pattern' => '89[1-579]\\d{6}',
        'example' => '891234567',
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
    'preferredInternationalPrefix' => '0~0',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '6[12][29]|(?:3[1-8]|4[136-8]|5[12457]|6[49])2|(?:56|65)[24]',
                '6[12][29]|(?:35|4[1378]|5[12457]|6[49])2|(?:56|65)[24]|(?:3[1-46-8]|46)2[013-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '3[1-8]|4(?:[1367]|[45][6-9]|8[4-6])|5(?:[1-5]|6[0135689]|7[4-6])|6(?:[12][3-7]|[459])',
                '3[1-8]|4(?:[1367]|[45][6-9]|8[4-6])|5(?:[1-5]|6(?:[015689]|3[02389])|7[4-6])|6(?:[12][3-7]|[459])',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[3-7]|89|9[1-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[89]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
