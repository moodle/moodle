<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SZ',
    'countryCode' => 268,
    'generalDesc' => [
        'pattern' => '0800\\d{4}|(?:[237]\\d|900)\\d{6}',
        'posLength' => [
            8,
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '[23][2-5]\\d{6}',
        'example' => '22171234',
        'posLength' => [
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '7[6-9]\\d{6}',
        'example' => '76123456',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '0800\\d{4}',
        'example' => '08001234',
        'posLength' => [
            8,
        ],
    ],
    'premiumRate' => [
        'pattern' => '900\\d{6}',
        'example' => '900012345',
        'posLength' => [
            9,
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
        'pattern' => '70\\d{6}',
        'example' => '70012345',
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
        'pattern' => '0800\\d{4}',
        'posLength' => [
            8,
        ],
    ],
    'internationalPrefix' => '00',
    'numberFormat' => [
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[0237]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{5})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '9',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
