<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'TJ',
    'countryCode' => 992,
    'generalDesc' => [
        'pattern' => '[0-57-9]\\d{8}',
        'posLength' => [
            9,
        ],
        'posLengthLocal' => [
            3,
            5,
            6,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:3(?:1[3-5]|2[245]|3[12]|4[24-7]|5[25]|72)|4(?:46|74|87))\\d{6}',
        'example' => '372123456',
        'posLengthLocal' => [
            3,
            5,
            6,
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:33[03-9]|4(?:1[18]|4[02-479])|81[1-9])\\d{6}|(?:[09]\\d|1[0178]|2[02]|[34]0|5[05]|7[01578]|8[078])\\d{7}',
        'example' => '917123456',
    ],
    'tollFree' => [
        'posLength' => [
            -1,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
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
    'internationalPrefix' => '810',
    'preferredInternationalPrefix' => '8~10',
    'numberFormat' => [
        [
            'pattern' => '(\\d{6})(\\d)(\\d{2})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '331',
                '3317',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '44[02-479]|[34]7',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d)(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '3(?:[1245]|3[12])',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[0-57-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
