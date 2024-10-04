<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SM',
    'countryCode' => 378,
    'generalDesc' => [
        'pattern' => '(?:0549|[5-7]\\d)\\d{6}',
        'posLength' => [
            8,
            10,
        ],
        'posLengthLocal' => [
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '0549(?:8[0157-9]|9\\d)\\d{4}',
        'example' => '0549886377',
        'posLength' => [
            10,
        ],
        'posLengthLocal' => [
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '6[16]\\d{6}',
        'example' => '66661212',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'posLength' => [
            -1,
        ],
    ],
    'premiumRate' => [
        'pattern' => '7[178]\\d{6}',
        'example' => '71123456',
        'posLength' => [
            8,
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
        'pattern' => '5[158]\\d{6}',
        'example' => '58001110',
        'posLength' => [
            8,
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
    'nationalPrefixForParsing' => '([89]\\d{5})$',
    'nationalPrefixTransformRule' => '0549$1',
    'numberFormat' => [
        [
            'pattern' => '(\\d{6})',
            'format' => '$1',
            'leadingDigitsPatterns' => [
                '[89]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[5-7]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '0',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'intlNumberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[5-7]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '0',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
