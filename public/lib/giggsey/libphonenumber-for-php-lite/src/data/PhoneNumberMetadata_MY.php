<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MY',
    'countryCode' => 60,
    'generalDesc' => [
        'pattern' => '1\\d{8,9}|(?:3\\d|[4-9])\\d{7}',
        'posLength' => [
            8,
            9,
            10,
        ],
        'posLengthLocal' => [
            6,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '4270\\d{4}|(?:3(?:2[0-36-9]|3[0-368]|4[0-278]|5[0-24-8]|6[0-467]|7[1246-9]|8\\d|9[0-57])\\d|4(?:2[0-689]|[3-79]\\d|8[1-35689])|5(?:2[0-589]|[3468]\\d|5[0-489]|7[1-9]|9[23])|6(?:2[2-9]|3[1357-9]|[46]\\d|5[0-6]|7[0-35-9]|85|9[015-8])|7(?:[2579]\\d|3[03-68]|4[0-8]|6[5-9]|8[0-35-9])|8(?:[24][2-8]|3[2-5]|5[2-7]|6[2-589]|7[2-578]|[89][2-9])|9(?:0[57]|13|[25-7]\\d|[3489][0-8]))\\d{5}',
        'example' => '323856789',
        'posLength' => [
            8,
            9,
        ],
        'posLengthLocal' => [
            6,
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '1(?:1888[689]|4400|8(?:47|8[27])[0-4])\\d{4}|1(?:0(?:[23568]\\d|4[0-6]|7[016-9]|9[0-8])|1(?:[1-5]\\d\\d|6(?:0[5-9]|[1-9]\\d)|7(?:[0-4]\\d|5[0-7]))|(?:[269]\\d|[37][1-9]|4[235-9])\\d|5(?:31|9\\d\\d)|8(?:1[23]|[236]\\d|4[06]|5(?:46|[7-9])|7[016-9]|8[01]|9[0-8]))\\d{5}',
        'example' => '123456789',
        'posLength' => [
            9,
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '1[378]00\\d{6}',
        'example' => '1300123456',
        'posLength' => [
            10,
        ],
    ],
    'premiumRate' => [
        'pattern' => '1600\\d{6}',
        'example' => '1600123456',
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
        'posLength' => [
            -1,
        ],
    ],
    'voip' => [
        'pattern' => '15(?:4(?:6[0-4]\\d|8(?:0[125]|[17]\\d|21|3[01]|4[01589]|5[014]|6[02]))|6(?:32[0-6]|78\\d))\\d{4}',
        'example' => '1546012345',
        'posLength' => [
            10,
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
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d)(\\d{3})(\\d{4})',
            'format' => '$1-$2 $3',
            'leadingDigitsPatterns' => [
                '[4-79]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3,4})',
            'format' => '$1-$2 $3',
            'leadingDigitsPatterns' => [
                '1(?:[02469]|[378][1-9]|53)|8',
                '1(?:[02469]|[37][1-9]|53|8(?:[1-46-9]|5[7-9]))|8',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{4})(\\d{4})',
            'format' => '$1-$2 $3',
            'leadingDigitsPatterns' => [
                '3',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3})(\\d{2})(\\d{4})',
            'format' => '$1-$2-$3-$4',
            'leadingDigitsPatterns' => [
                '1(?:[367]|80)',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
            'format' => '$1-$2 $3',
            'leadingDigitsPatterns' => [
                '15',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{4})',
            'format' => '$1-$2 $3',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
