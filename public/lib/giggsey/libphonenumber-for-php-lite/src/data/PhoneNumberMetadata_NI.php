<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'NI',
    'countryCode' => 505,
    'generalDesc' => [
        'pattern' => '(?:1800|[25-8]\\d{3})\\d{4}',
        'posLength' => [
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2\\d{7}',
        'example' => '21234567',
    ],
    'mobile' => [
        'pattern' => '(?:5(?:5[0-7]|[78]\\d)|6(?:20|3[035]|4[045]|5[05]|77|8[1-9]|9[059])|(?:7[5-8]|8\\d)\\d)\\d{5}',
        'example' => '81234567',
    ],
    'tollFree' => [
        'pattern' => '1800\\d{4}',
        'example' => '18001234',
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
    'numberFormat' => [
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[125-8]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
