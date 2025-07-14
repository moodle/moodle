<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'PF',
    'countryCode' => 689,
    'generalDesc' => [
        'pattern' => '4\\d{5}(?:\\d{2})?|8\\d{7,8}',
        'posLength' => [
            6,
            8,
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '4(?:0[4-689]|9[4-68])\\d{5}',
        'example' => '40412345',
        'posLength' => [
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '8[7-9]\\d{6}',
        'example' => '87123456',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '80[0-5]\\d{6}',
        'example' => '800012345',
        'posLength' => [
            9,
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
        'pattern' => '499\\d{5}',
        'example' => '49901234',
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
        'pattern' => '44\\d{4}',
        'example' => '440123',
        'posLength' => [
            6,
        ],
    ],
    'voicemail' => [
        'posLength' => [
            -1,
        ],
    ],
    'noInternationalDialling' => [
        'pattern' => '44\\d{4}',
        'posLength' => [
            6,
        ],
    ],
    'internationalPrefix' => '00',
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '44',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '4|8[7-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
