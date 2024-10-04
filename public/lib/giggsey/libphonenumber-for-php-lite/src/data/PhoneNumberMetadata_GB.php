<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GB',
    'countryCode' => 44,
    'generalDesc' => [
        'pattern' => '[1-357-9]\\d{9}|[18]\\d{8}|8\\d{6}',
        'posLength' => [
            7,
            9,
            10,
        ],
        'posLengthLocal' => [
            4,
            5,
            6,
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:1(?:1(?:3(?:[0-58]\\d\\d|73[0-35])|4(?:(?:[0-5]\\d|70)\\d|69[7-9])|(?:(?:5[0-26-9]|[78][0-49])\\d|6(?:[0-4]\\d|50))\\d)|(?:2(?:(?:0[024-9]|2[3-9]|3[3-79]|4[1-689]|[58][02-9]|6[0-47-9]|7[013-9]|9\\d)\\d|1(?:[0-7]\\d|8[0-3]))|(?:3(?:0\\d|1[0-8]|[25][02-9]|3[02-579]|[468][0-46-9]|7[1-35-79]|9[2-578])|4(?:0[03-9]|[137]\\d|[28][02-57-9]|4[02-69]|5[0-8]|[69][0-79])|5(?:0[1-35-9]|[16]\\d|2[024-9]|3[015689]|4[02-9]|5[03-9]|7[0-35-9]|8[0-468]|9[0-57-9])|6(?:0[034689]|1\\d|2[0-35689]|[38][013-9]|4[1-467]|5[0-69]|6[13-9]|7[0-8]|9[0-24578])|7(?:0[0246-9]|2\\d|3[0236-8]|4[03-9]|5[0-46-9]|6[013-9]|7[0-35-9]|8[024-9]|9[02-9])|8(?:0[35-9]|2[1-57-9]|3[02-578]|4[0-578]|5[124-9]|6[2-69]|7\\d|8[02-9]|9[02569])|9(?:0[02-589]|[18]\\d|2[02-689]|3[1-57-9]|4[2-9]|5[0-579]|6[2-47-9]|7[0-24578]|9[2-57]))\\d)\\d)|2(?:0[013478]|3[0189]|4[017]|8[0-46-9]|9[0-2])\\d{3})\\d{4}|1(?:2(?:0(?:46[1-4]|87[2-9])|545[1-79]|76(?:2\\d|3[1-8]|6[1-6])|9(?:7(?:2[0-4]|3[2-5])|8(?:2[2-8]|7[0-47-9]|8[3-5])))|3(?:6(?:38[2-5]|47[23])|8(?:47[04-9]|64[0157-9]))|4(?:044[1-7]|20(?:2[23]|8\\d)|6(?:0(?:30|5[2-57]|6[1-8]|7[2-8])|140)|8(?:052|87[1-3]))|5(?:2(?:4(?:3[2-79]|6\\d)|76\\d)|6(?:26[06-9]|686))|6(?:06(?:4\\d|7[4-79])|295[5-7]|35[34]\\d|47(?:24|61)|59(?:5[08]|6[67]|74)|9(?:55[0-4]|77[23]))|7(?:26(?:6[13-9]|7[0-7])|(?:442|688)\\d|50(?:2[0-3]|[3-68]2|76))|8(?:27[56]\\d|37(?:5[2-5]|8[239])|843[2-58])|9(?:0(?:0(?:6[1-8]|85)|52\\d)|3583|4(?:66[1-8]|9(?:2[01]|81))|63(?:23|3[1-4])|9561))\\d{3}',
        'example' => '1212345678',
        'posLength' => [
            9,
            10,
        ],
        'posLengthLocal' => [
            4,
            5,
            6,
            7,
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '7(?:457[0-57-9]|700[01]|911[028])\\d{5}|7(?:[1-3]\\d\\d|4(?:[0-46-9]\\d|5[0-689])|5(?:0[0-8]|[13-9]\\d|2[0-35-9])|7(?:0[1-9]|[1-7]\\d|8[02-9]|9[0-689])|8(?:[014-9]\\d|[23][0-8])|9(?:[024-9]\\d|1[02-9]|3[0-689]))\\d{6}',
        'example' => '7400123456',
        'posLength' => [
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '80[08]\\d{7}|800\\d{6}|8001111',
        'example' => '8001234567',
    ],
    'premiumRate' => [
        'pattern' => '(?:8(?:4[2-5]|7[0-3])|9(?:[01]\\d|8[2-49]))\\d{7}|845464\\d',
        'example' => '9012345678',
        'posLength' => [
            7,
            10,
        ],
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'pattern' => '70\\d{8}',
        'example' => '7012345678',
        'posLength' => [
            10,
        ],
    ],
    'voip' => [
        'pattern' => '56\\d{8}',
        'example' => '5612345678',
        'posLength' => [
            10,
        ],
    ],
    'pager' => [
        'pattern' => '76(?:464|652)\\d{5}|76(?:0[0-28]|2[356]|34|4[01347]|5[49]|6[0-369]|77|8[14]|9[139])\\d{6}',
        'example' => '7640123456',
        'posLength' => [
            10,
        ],
    ],
    'uan' => [
        'pattern' => '(?:3[0347]|55)\\d{8}',
        'example' => '5512345678',
        'posLength' => [
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
    'preferredExtnPrefix' => ' x',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '800',
                '8001',
                '80011',
                '800111',
                '8001111',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '845',
                '8454',
                '84546',
                '845464',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '800',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{5})(\\d{4,5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1(?:38|5[23]|69|76|94)',
                '1(?:(?:38|69)7|5(?:24|39)|768|946)',
                '1(?:3873|5(?:242|39[4-6])|(?:697|768)[347]|9467)',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{5,6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1(?:[2-69][02-9]|[78])',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[25]|7(?:0|6[02-9])',
                '[25]|7(?:0|6(?:[03-9]|2[356]))',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '7',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[1389]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mainCountryForCode' => true,
    'mobileNumberPortableRegion' => true,
];
