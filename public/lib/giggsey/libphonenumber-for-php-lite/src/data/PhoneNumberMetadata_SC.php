<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SC',
    'countryCode' => 248,
    'generalDesc' => [
        'pattern' => '(?:[2489]\\d|64)\\d{5}',
        'posLength' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '4[2-46]\\d{5}',
        'example' => '4217123',
    ],
    'mobile' => [
        'pattern' => '2[125-8]\\d{5}',
        'example' => '2510123',
    ],
    'tollFree' => [
        'pattern' => '800[08]\\d{3}',
        'example' => '8000000',
    ],
    'premiumRate' => [
        'pattern' => '85\\d{5}',
        'example' => '8512345',
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
        'pattern' => '971\\d{4}|(?:64|95)\\d{5}',
        'example' => '6412345',
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
    'internationalPrefix' => '010|0[0-2]',
    'preferredInternationalPrefix' => '00',
    'numberFormat' => [
        [
            'pattern' => '(\\d)(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[246]|9[57]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
