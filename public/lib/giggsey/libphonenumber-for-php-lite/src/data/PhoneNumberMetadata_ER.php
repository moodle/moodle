<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'ER',
    'countryCode' => 291,
    'generalDesc' => [
        'pattern' => '[178]\\d{6}',
        'posLength' => [
            7,
        ],
        'posLengthLocal' => [
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:1(?:1[12568]|[24]0|55|6[146])|8\\d\\d)\\d{4}',
        'example' => '8370362',
        'posLengthLocal' => [
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:17[1-3]|7\\d\\d)\\d{4}',
        'example' => '7123456',
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
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d)(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[178]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
