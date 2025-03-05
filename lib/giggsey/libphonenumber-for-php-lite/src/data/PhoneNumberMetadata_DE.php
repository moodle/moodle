<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'DE',
    'countryCode' => 49,
    'generalDesc' => [
        'pattern' => '[2579]\\d{5,14}|49(?:[34]0|69|8\\d)\\d\\d?|49(?:37|49|60|7[089]|9\\d)\\d{1,3}|49(?:2[024-9]|3[2-689]|7[1-7])\\d{1,8}|(?:1|[368]\\d|4[0-8])\\d{3,13}|49(?:[015]\\d|2[13]|31|[46][1-8])\\d{1,9}',
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
            14,
            15,
        ],
        'posLengthLocal' => [
            2,
            3,
        ],
    ],
    'fixedLine' => [
        'pattern' => '32\\d{9,11}|49[1-6]\\d{10}|322\\d{6}|49[0-7]\\d{3,9}|(?:[34]0|[68]9)\\d{3,13}|(?:2(?:0[1-689]|[1-3569]\\d|4[0-8]|7[1-7]|8[0-7])|3(?:[3569]\\d|4[0-79]|7[1-7]|8[1-8])|4(?:1[02-9]|[2-48]\\d|5[0-6]|6[0-8]|7[0-79])|5(?:0[2-8]|[124-6]\\d|[38][0-8]|[79][0-7])|6(?:0[02-9]|[1-358]\\d|[47][0-8]|6[1-9])|7(?:0[2-8]|1[1-9]|[27][0-7]|3\\d|[4-6][0-8]|8[0-5]|9[013-7])|8(?:0[2-9]|1[0-79]|2\\d|3[0-46-9]|4[0-6]|5[013-9]|6[1-8]|7[0-8]|8[0-24-6])|9(?:0[6-9]|[1-4]\\d|[589][0-7]|6[0-8]|7[0-467]))\\d{3,12}',
        'example' => '30123456',
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
            14,
            15,
        ],
        'posLengthLocal' => [
            2,
            3,
            4,
        ],
    ],
    'mobile' => [
        'pattern' => '1(?:(?:5(?:[0-25-9]\\d\\d|310)|76\\d\\d)\\d{6}|6[023]\\d{7,8})|17\\d{8}',
        'example' => '15123456789',
        'posLength' => [
            10,
            11,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{7,12}',
        'example' => '8001234567890',
        'posLength' => [
            10,
            11,
            12,
            13,
            14,
            15,
        ],
    ],
    'premiumRate' => [
        'pattern' => '(?:137[7-9]|900(?:[135]|9\\d))\\d{6}',
        'example' => '9001234567',
        'posLength' => [
            10,
            11,
        ],
    ],
    'sharedCost' => [
        'pattern' => '180\\d{5,11}|13(?:7[1-6]\\d\\d|8)\\d{4}',
        'example' => '18012345',
        'posLength' => [
            7,
            8,
            9,
            10,
            11,
            12,
            13,
            14,
        ],
    ],
    'personalNumber' => [
        'pattern' => '700\\d{8}',
        'example' => '70012345678',
        'posLength' => [
            11,
        ],
    ],
    'voip' => [
        'posLength' => [
            -1,
        ],
    ],
    'pager' => [
        'pattern' => '16(?:4\\d{1,10}|[89]\\d{1,11})',
        'example' => '16412345',
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
            14,
        ],
    ],
    'uan' => [
        'pattern' => '18(?:1\\d{5,11}|[2-9]\\d{8})',
        'example' => '18500123456',
        'posLength' => [
            8,
            9,
            10,
            11,
            12,
            13,
            14,
        ],
    ],
    'voicemail' => [
        'pattern' => '1(?:6(?:013|255|399)|7(?:(?:[015]1|[69]3)3|[2-4]55|[78]99))\\d{7,8}|15(?:(?:[03-68]00|113)\\d|2\\d55|7\\d99|9\\d33)\\d{7}',
        'example' => '177991234567',
        'posLength' => [
            12,
            13,
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
            'pattern' => '(\\d{2})(\\d{3,13})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '3[02]|40|[68]9',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3,12})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '2(?:0[1-389]|1[124]|2[18]|3[14])|3(?:[35-9][15]|4[015])|906|(?:2[4-9]|4[2-9]|[579][1-9]|[68][1-8])1',
                '2(?:0[1-389]|12[0-8])|3(?:[35-9][15]|4[015])|906|2(?:[13][14]|2[18])|(?:2[4-9]|4[2-9]|[579][1-9]|[68][1-8])1',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{2,11})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[24-6]|3(?:[3569][02-46-9]|4[2-4679]|7[2-467]|8[2-46-8])|70[2-8]|8(?:0[2-9]|[1-8])|90[7-9]|[79][1-9]',
                '[24-6]|3(?:3(?:0[1-467]|2[127-9]|3[124578]|7[1257-9]|8[1256]|9[145])|4(?:2[135]|4[13578]|9[1346])|5(?:0[14]|2[1-3589]|6[1-4]|7[13468]|8[13568])|6(?:2[1-489]|3[124-6]|6[13]|7[12579]|8[1-356]|9[135])|7(?:2[1-7]|4[145]|6[1-5]|7[1-4])|8(?:21|3[1468]|6|7[1467]|8[136])|9(?:0[12479]|2[1358]|4[134679]|6[1-9]|7[136]|8[147]|9[1468]))|70[2-8]|8(?:0[2-9]|[1-8])|90[7-9]|[79][1-9]|3[68]4[1347]|3(?:47|60)[1356]|3(?:3[46]|46|5[49])[1246]|3[4579]3[1357]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '138',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{5})(\\d{2,10})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '3',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{5,11})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '181',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d)(\\d{4,10})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '1(?:3|80)|9',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{7,8})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1[67]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{7,12})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{5})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '185',
                '1850',
                '18500',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{4})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '7',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '18[68]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '15[1279]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{5})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '15[03568]',
                '15(?:[0568]|31)',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{8})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '18',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{7,8})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '1(?:6[023]|7)',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{2})(\\d{7})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '15[279]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{8})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '15',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
