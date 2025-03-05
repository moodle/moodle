<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GT',
    'countryCode' => 502,
    'generalDesc' => [
        'pattern' => '80\\d{6}|(?:1\\d{3}|[2-7])\\d{7}',
        'posLength' => [
            8,
            11,
        ],
    ],
    'fixedLine' => [
        'pattern' => '[267][2-9]\\d{6}',
        'example' => '22456789',
        'posLength' => [
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:[3-5]\\d\\d|80[0-4])\\d{5}',
        'example' => '51234567',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '18[01]\\d{8}',
        'example' => '18001112222',
        'posLength' => [
            11,
        ],
    ],
    'premiumRate' => [
        'pattern' => '19\\d{9}',
        'example' => '19001112222',
        'posLength' => [
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
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[2-8]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
