<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'NF',
    'countryCode' => 672,
    'generalDesc' => [
        'pattern' => '[13]\\d{5}',
        'posLength' => [
            6,
        ],
        'posLengthLocal' => [
            5,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:1(?:06|17|28|39)|3[0-2]\\d)\\d{3}',
        'example' => '106609',
        'posLengthLocal' => [
            5,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:14|3[58])\\d{4}',
        'example' => '381234',
        'posLengthLocal' => [
            5,
        ],
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
    'internationalPrefix' => '00',
    'nationalPrefixForParsing' => '([0-258]\\d{4})$',
    'nationalPrefixTransformRule' => '3$1',
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1[0-3]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[13]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
