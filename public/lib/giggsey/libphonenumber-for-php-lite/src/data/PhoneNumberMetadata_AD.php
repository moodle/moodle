<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'AD',
    'countryCode' => 376,
    'generalDesc' => [
        'pattern' => '(?:1|6\\d)\\d{7}|[135-9]\\d{5}',
        'posLength' => [
            6,
            8,
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '[78]\\d{5}',
        'example' => '712345',
        'posLength' => [
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '690\\d{6}|[356]\\d{5}',
        'example' => '312345',
        'posLength' => [
            6,
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '180[02]\\d{4}',
        'example' => '18001234',
        'posLength' => [
            8,
        ],
    ],
    'premiumRate' => [
        'pattern' => '[19]\\d{5}',
        'example' => '912345',
        'posLength' => [
            6,
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
        'pattern' => '1800\\d{4}',
        'posLength' => [
            8,
        ],
    ],
    'internationalPrefix' => '00',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{3})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[135-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '6',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
