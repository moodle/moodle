<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'PG',
    'countryCode' => 675,
    'generalDesc' => [
        'pattern' => '(?:180|[78]\\d{3})\\d{4}|(?:[2-589]\\d|64)\\d{5}',
        'posLength' => [
            7,
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:(?:3[0-2]|4[257]|5[34]|9[78])\\d|64[1-9]|85[02-46-9])\\d{4}',
        'example' => '3123456',
        'posLength' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:7\\d|8[1-38])\\d{6}',
        'example' => '70123456',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '180\\d{4}',
        'example' => '1801234',
        'posLength' => [
            7,
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
        'pattern' => '2(?:0[0-57]|7[568])\\d{4}',
        'example' => '2751234',
        'posLength' => [
            7,
        ],
    ],
    'pager' => [
        'pattern' => '27[01]\\d{4}',
        'example' => '2700123',
        'posLength' => [
            7,
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
    'internationalPrefix' => '00|140[1-3]',
    'preferredInternationalPrefix' => '00',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '18|[2-69]|85',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[78]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
