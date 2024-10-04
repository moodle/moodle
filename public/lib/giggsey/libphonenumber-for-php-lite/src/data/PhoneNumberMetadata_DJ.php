<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'DJ',
    'countryCode' => 253,
    'generalDesc' => [
        'pattern' => '(?:2\\d|77)\\d{6}',
        'posLength' => [
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2(?:1[2-5]|7[45])\\d{5}',
        'example' => '21360003',
    ],
    'mobile' => [
        'pattern' => '77\\d{6}',
        'example' => '77831001',
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
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[27]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
