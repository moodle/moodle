<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'ZA',
    'countryCode' => 27,
    'generalDesc' => [
        'pattern' => '[1-79]\\d{8}|8\\d{4,9}',
        'posLength' => [
            5,
            6,
            7,
            8,
            9,
            10,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2(?:0330|4302)|52087)0\\d{3}|(?:1[0-8]|2[1-378]|3[1-69]|4\\d|5[1346-8])\\d{7}',
        'example' => '101234567',
        'posLength' => [
            9,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:1(?:3492[0-25]|4495[0235]|549(?:20|5[01]))|4[34]492[01])\\d{3}|8[1-4]\\d{3,7}|(?:2[27]|47|54)4950\\d{3}|(?:1(?:049[2-4]|9[12]\\d\\d)|(?:6\\d\\d|7(?:[0-46-9]\\d|5[0-4]))\\d\\d|8(?:5\\d{3}|7(?:08[67]|158|28[5-9]|310)))\\d{4}|(?:1[6-8]|28|3[2-69]|4[025689]|5[36-8])4920\\d{3}|(?:12|[2-5]1)492\\d{4}',
        'example' => '711234567',
        'posLength' => [
            5,
            6,
            7,
            8,
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '80\\d{7}',
        'example' => '801234567',
        'posLength' => [
            9,
        ],
    ],
    'premiumRate' => [
        'pattern' => '(?:86[2-9]|9[0-2]\\d)\\d{6}',
        'example' => '862345678',
        'posLength' => [
            9,
        ],
    ],
    'sharedCost' => [
        'pattern' => '860\\d{6}',
        'example' => '860123456',
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
        'pattern' => '87(?:08[0-589]|15[0-79]|28[0-4]|31[1-9])\\d{4}|87(?:[02][0-79]|1[0-46-9]|3[02-9]|[4-9]\\d)\\d{5}',
        'example' => '871234567',
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
        'pattern' => '861\\d{6,7}',
        'example' => '861123456',
        'posLength' => [
            9,
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
            'pattern' => '(\\d{2})(\\d{3,4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '8[1-4]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{2,3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '8[1-4]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '860',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[1-9]',
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
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
