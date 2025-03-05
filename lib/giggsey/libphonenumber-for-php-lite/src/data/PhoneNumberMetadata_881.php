<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => '001',
    'countryCode' => 881,
    'generalDesc' => [
        'pattern' => '6\\d{9}|[0-36-9]\\d{8}',
        'posLength' => [
            9,
            10,
        ],
    ],
    'fixedLine' => [
        'posLength' => [
            -1,
        ],
    ],
    'mobile' => [
        'pattern' => '6\\d{9}|[0-36-9]\\d{8}',
        'example' => '612345678',
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
    'internationalPrefix' => '',
    'numberFormat' => [
        [
            'pattern' => '(\\d)(\\d{3})(\\d{5})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[0-37-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3})(\\d{5,6})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '6',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
