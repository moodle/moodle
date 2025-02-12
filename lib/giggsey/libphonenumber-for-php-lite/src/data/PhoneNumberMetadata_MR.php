<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MR',
    'countryCode' => 222,
    'generalDesc' => [
        'pattern' => '(?:[2-4]\\d\\d|800)\\d{5}',
        'posLength' => [
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:25[08]|35\\d|45[1-7])\\d{5}',
        'example' => '35123456',
    ],
    'mobile' => [
        'pattern' => '[2-4][0-46-9]\\d{6}',
        'example' => '22123456',
    ],
    'tollFree' => [
        'pattern' => '800\\d{5}',
        'example' => '80012345',
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
                '[2-48]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
