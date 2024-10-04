<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SV',
    'countryCode' => 503,
    'generalDesc' => [
        'pattern' => '[267]\\d{7}|(?:80\\d|900)\\d{4}(?:\\d{4})?',
        'posLength' => [
            7,
            8,
            11,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2(?:79(?:0[0347-9]|[1-9]\\d)|89(?:0[024589]|[1-9]\\d))\\d{3}|2(?:[1-69]\\d|[78][0-8])\\d{5}',
        'example' => '21234567',
        'posLength' => [
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '[67]\\d{7}',
        'example' => '70123456',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{8}|80[01]\\d{4}',
        'example' => '8001234',
        'posLength' => [
            7,
            11,
        ],
    ],
    'premiumRate' => [
        'pattern' => '900\\d{4}(?:\\d{4})?',
        'example' => '9001234',
        'posLength' => [
            7,
            11,
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
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[89]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[267]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{4})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[89]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
