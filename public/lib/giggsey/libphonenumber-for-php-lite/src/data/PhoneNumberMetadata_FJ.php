<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'FJ',
    'countryCode' => 679,
    'generalDesc' => [
        'pattern' => '45\\d{5}|(?:0800\\d|[235-9])\\d{6}',
        'posLength' => [
            7,
            11,
        ],
    ],
    'fixedLine' => [
        'pattern' => '603\\d{4}|(?:3[0-5]|6[25-7]|8[58])\\d{5}',
        'example' => '3212345',
        'posLength' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:[279]\\d|45|5[01568]|8[034679])\\d{5}',
        'example' => '7012345',
        'posLength' => [
            7,
        ],
    ],
    'tollFree' => [
        'pattern' => '0800\\d{7}',
        'example' => '08001234567',
        'posLength' => [
            11,
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
    'internationalPrefix' => '0(?:0|52)',
    'preferredInternationalPrefix' => '00',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[235-9]|45',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '0',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
