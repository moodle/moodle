<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'TL',
    'countryCode' => 670,
    'generalDesc' => [
        'pattern' => '7\\d{7}|(?:[2-47]\\d|[89]0)\\d{5}',
        'posLength' => [
            7,
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2[1-5]|3[1-9]|4[1-4])\\d{5}',
        'example' => '2112345',
        'posLength' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '7[2-8]\\d{6}',
        'example' => '77212345',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '80\\d{5}',
        'example' => '8012345',
        'posLength' => [
            7,
        ],
    ],
    'premiumRate' => [
        'pattern' => '90\\d{5}',
        'example' => '9012345',
        'posLength' => [
            7,
        ],
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'pattern' => '70\\d{5}',
        'example' => '7012345',
        'posLength' => [
            7,
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
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[2-489]|70',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '7',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
