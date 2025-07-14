<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'VU',
    'countryCode' => 678,
    'generalDesc' => [
        'pattern' => '[57-9]\\d{6}|(?:[238]\\d|48)\\d{3}',
        'posLength' => [
            5,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:38[0-8]|48[4-9])\\d\\d|(?:2[02-9]|3[4-7]|88)\\d{3}',
        'example' => '22123',
        'posLength' => [
            5,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:[58]\\d|7[013-7])\\d{5}',
        'example' => '5912345',
        'posLength' => [
            7,
        ],
    ],
    'tollFree' => [
        'pattern' => '81[18]\\d\\d',
        'example' => '81123',
        'posLength' => [
            5,
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
        'pattern' => '9(?:0[1-9]|1[01])\\d{4}',
        'example' => '9010123',
        'posLength' => [
            7,
        ],
    ],
    'pager' => [
        'posLength' => [
            -1,
        ],
    ],
    'uan' => [
        'pattern' => '(?:3[03]|900\\d)\\d{3}',
        'example' => '30123',
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
                '[57-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
