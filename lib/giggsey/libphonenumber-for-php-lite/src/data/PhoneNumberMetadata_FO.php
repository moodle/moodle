<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'FO',
    'countryCode' => 298,
    'generalDesc' => [
        'pattern' => '[2-9]\\d{5}',
        'posLength' => [
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:20|[34]\\d|8[19])\\d{4}',
        'example' => '201234',
    ],
    'mobile' => [
        'pattern' => '(?:[27][1-9]|5\\d|9[16])\\d{4}',
        'example' => '211234',
    ],
    'tollFree' => [
        'pattern' => '80[257-9]\\d{3}',
        'example' => '802123',
    ],
    'premiumRate' => [
        'pattern' => '90(?:[13-5][15-7]|2[125-7]|9\\d)\\d\\d',
        'example' => '901123',
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
        'pattern' => '(?:6[0-36]|88)\\d{4}',
        'example' => '601234',
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
    'nationalPrefixForParsing' => '(10(?:01|[12]0|88))',
    'numberFormat' => [
        [
            'pattern' => '(\\d{6})',
            'format' => '$1',
            'leadingDigitsPatterns' => [
                '[2-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
    ],
];
