<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CU',
    'countryCode' => 53,
    'generalDesc' => [
        'pattern' => '(?:[2-7]|8\\d\\d)\\d{7}|[2-47]\\d{6}|[34]\\d{5}',
        'posLength' => [
            6,
            7,
            8,
            10,
        ],
        'posLengthLocal' => [
            4,
            5,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:3[23]|4[89])\\d{4,6}|(?:31|4[36]|8(?:0[25]|78)\\d)\\d{6}|(?:2[1-4]|4[1257]|7\\d)\\d{5,6}',
        'example' => '71234567',
        'posLengthLocal' => [
            4,
            5,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:5\\d|6[2-4])\\d{6}',
        'example' => '51234567',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{7}',
        'example' => '8001234567',
        'posLength' => [
            10,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'sharedCost' => [
        'pattern' => '807\\d{7}',
        'example' => '8071234567',
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
    'internationalPrefix' => '119',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{4,6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '2[1-4]|[34]',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{6,7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '7',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[56]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
