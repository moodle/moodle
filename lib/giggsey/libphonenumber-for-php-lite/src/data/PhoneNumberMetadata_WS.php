<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'WS',
    'countryCode' => 685,
    'generalDesc' => [
        'pattern' => '(?:[2-6]|8\\d{5})\\d{4}|[78]\\d{6}|[68]\\d{5}',
        'posLength' => [
            5,
            6,
            7,
            10,
        ],
    ],
    'fixedLine' => [
        'pattern' => '6[1-9]\\d{3}|(?:[2-5]|60)\\d{4}',
        'example' => '22123',
        'posLength' => [
            5,
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:7[1-35-7]|8(?:[3-7]|9\\d{3}))\\d{5}',
        'example' => '7212345',
        'posLength' => [
            7,
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{3}',
        'example' => '800123',
        'posLength' => [
            6,
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
    'internationalPrefix' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{5})',
            'format' => '$1',
            'leadingDigitsPatterns' => [
                '[2-5]|6[1-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3,7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[68]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '7',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
