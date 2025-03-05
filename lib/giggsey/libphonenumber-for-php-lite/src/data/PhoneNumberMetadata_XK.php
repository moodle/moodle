<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'XK',
    'countryCode' => 383,
    'generalDesc' => [
        'pattern' => '2\\d{7,8}|3\\d{7,11}|(?:4\\d\\d|[89]00)\\d{5}',
        'posLength' => [
            8,
            9,
            10,
            11,
            12,
        ],
    ],
    'fixedLine' => [
        'pattern' => '38\\d{6,10}|(?:2[89]|39)(?:0\\d{5,6}|[1-9]\\d{5})',
        'example' => '28012345',
    ],
    'mobile' => [
        'pattern' => '4[3-9]\\d{6}',
        'example' => '43201234',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{5}',
        'example' => '80001234',
        'posLength' => [
            8,
        ],
    ],
    'premiumRate' => [
        'pattern' => '900\\d{5}',
        'example' => '90001234',
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
    'internationalPrefix' => '00',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[89]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[2-4]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '2|39',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{7,10})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '3',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
