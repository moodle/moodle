<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'AE',
    'countryCode' => 971,
    'generalDesc' => [
        'pattern' => '(?:[4-7]\\d|9[0-689])\\d{7}|800\\d{2,9}|[2-4679]\\d{7}',
        'posLength' => [
            5,
            6,
            7,
            8,
            9,
            10,
            11,
            12,
        ],
    ],
    'fixedLine' => [
        'pattern' => '[2-4679][2-8]\\d{6}',
        'example' => '22345678',
        'posLength' => [
            8,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '5[024-68]\\d{7}',
        'example' => '501234567',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '400\\d{6}|800\\d{2,9}',
        'example' => '800123456',
    ],
    'premiumRate' => [
        'pattern' => '900[02]\\d{5}',
        'example' => '900234567',
        'posLength' => [
            9,
        ],
    ],
    'sharedCost' => [
        'pattern' => '700[05]\\d{5}',
        'example' => '700012345',
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
        'pattern' => '600[25]\\d{5}',
        'example' => '600212345',
        'posLength' => [
            9,
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
            'pattern' => '(\\d{3})(\\d{2,9})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '60|8',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[236]|[479][2-8]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d)(\\d{5})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[479]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '5',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
