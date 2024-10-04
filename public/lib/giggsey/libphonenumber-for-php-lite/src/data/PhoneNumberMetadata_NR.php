<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'NR',
    'countryCode' => 674,
    'generalDesc' => [
        'pattern' => '(?:444|(?:55|8\\d)\\d|666)\\d{4}',
        'posLength' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '444\\d{4}',
        'example' => '4441234',
    ],
    'mobile' => [
        'pattern' => '(?:55[3-9]|666|8\\d\\d)\\d{4}',
        'example' => '5551234',
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
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[4-68]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
