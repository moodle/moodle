<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'NC',
    'countryCode' => 687,
    'generalDesc' => [
        'pattern' => '(?:050|[2-57-9]\\d\\d)\\d{3}',
        'posLength' => [
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2[03-9]|3[0-5]|4[1-7]|88)\\d{4}',
        'example' => '201234',
    ],
    'mobile' => [
        'pattern' => '(?:[579]\\d|8[0-79])\\d{4}',
        'example' => '751234',
    ],
    'tollFree' => [
        'pattern' => '050\\d{3}',
        'example' => '050012',
    ],
    'premiumRate' => [
        'pattern' => '36\\d{4}',
        'example' => '366711',
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
            'pattern' => '(\\d{3})',
            'format' => '$1',
            'leadingDigitsPatterns' => [
                '5[6-8]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1.$2.$3',
            'leadingDigitsPatterns' => [
                '[02-57-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'intlNumberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1.$2.$3',
            'leadingDigitsPatterns' => [
                '[02-57-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
