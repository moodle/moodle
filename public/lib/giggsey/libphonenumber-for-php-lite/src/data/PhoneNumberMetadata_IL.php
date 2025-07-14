<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'IL',
    'countryCode' => 972,
    'generalDesc' => [
        'pattern' => '1\\d{6}(?:\\d{3,5})?|[57]\\d{8}|[1-489]\\d{7}',
        'posLength' => [
            7,
            8,
            9,
            10,
            11,
            12,
        ],
    ],
    'fixedLine' => [
        'pattern' => '153\\d{8,9}|29[1-9]\\d{5}|(?:2[0-8]|[3489]\\d)\\d{6}',
        'example' => '21234567',
        'posLength' => [
            8,
            11,
            12,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '55(?:4(?:[01]0|5[0-2])|57[0-289])\\d{4}|5(?:(?:[0-2][02-9]|[36]\\d|[49][2-9]|8[3-7])\\d|5(?:01|2\\d|3[0-3]|4[34]|5[0-25689]|6[6-8]|7[0-267]|8[7-9]|9[1-9]))\\d{5}',
        'example' => '502345678',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:255|80[019]\\d{3})\\d{3}',
        'example' => '1800123456',
        'posLength' => [
            7,
            10,
        ],
    ],
    'premiumRate' => [
        'pattern' => '1212\\d{4}|1(?:200|9(?:0[0-2]|19))\\d{6}',
        'example' => '1919123456',
        'posLength' => [
            8,
            10,
        ],
    ],
    'sharedCost' => [
        'pattern' => '1700\\d{6}',
        'example' => '1700123456',
        'posLength' => [
            10,
        ],
    ],
    'personalNumber' => [
        'posLength' => [
            -1,
        ],
    ],
    'voip' => [
        'pattern' => '7(?:38(?:0\\d|5[0-3569]|88)|8(?:33|55|77|81)\\d)\\d{4}|7(?:18|2[23]|3[237]|47|6[258]|7\\d|82|9[2-9])\\d{6}',
        'example' => '771234567',
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
        'pattern' => '1599\\d{6}',
        'example' => '1599123456',
        'posLength' => [
            10,
        ],
    ],
    'voicemail' => [
        'pattern' => '151\\d{8,9}',
        'example' => '15112340000',
        'posLength' => [
            11,
            12,
        ],
    ],
    'noInternationalDialling' => [
        'pattern' => '1700\\d{6}',
        'posLength' => [
            10,
        ],
    ],
    'internationalPrefix' => '0(?:0|1[2-9])',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{4})(\\d{3})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '125',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{2})(\\d{2})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '121',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '[2-489]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '[57]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{3})(\\d{3})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '12',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{6})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '159',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1-$2-$3-$4',
            'leadingDigitsPatterns' => [
                '1[7-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{1,2})(\\d{3})(\\d{4})',
            'format' => '$1-$2 $3-$4',
            'leadingDigitsPatterns' => [
                '15',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
