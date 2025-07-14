<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GL',
    'countryCode' => 299,
    'generalDesc' => [
        'pattern' => '(?:19|[2-689]\\d|70)\\d{4}',
        'posLength' => [
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:19|3[1-7]|[68][1-9]|70|9\\d)\\d{4}',
        'example' => '321000',
    ],
    'mobile' => [
        'pattern' => '[245]\\d{5}',
        'example' => '221234',
    ],
    'tollFree' => [
        'pattern' => '80\\d{4}',
        'example' => '801234',
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
        'pattern' => '3[89]\\d{4}',
        'example' => '381234',
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
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '19|[2-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
