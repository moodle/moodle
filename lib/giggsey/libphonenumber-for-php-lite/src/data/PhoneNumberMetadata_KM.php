<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'KM',
    'countryCode' => 269,
    'generalDesc' => [
        'pattern' => '[3478]\\d{6}',
        'posLength' => [
            7,
        ],
        'posLengthLocal' => [
            4,
        ],
    ],
    'fixedLine' => [
        'pattern' => '7[4-7]\\d{5}',
        'example' => '7712345',
        'posLengthLocal' => [
            4,
        ],
    ],
    'mobile' => [
        'pattern' => '[34]\\d{6}',
        'example' => '3212345',
    ],
    'tollFree' => [
        'posLength' => [
            -1,
        ],
    ],
    'premiumRate' => [
        'pattern' => '8\\d{6}',
        'example' => '8001234',
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
            'pattern' => '(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[3478]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
