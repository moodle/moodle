<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'VE',
    'countryCode' => 58,
    'generalDesc' => [
        'pattern' => '[68]00\\d{7}|(?:[24]\\d|[59]0)\\d{8}',
        'posLength' => [
            10,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2(?:12|3[457-9]|[467]\\d|[58][1-9]|9[1-6])|[4-6]00)\\d{7}',
        'example' => '2121234567',
        'posLengthLocal' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '4(?:1[24-8]|2[46])\\d{7}',
        'example' => '4121234567',
    ],
    'tollFree' => [
        'pattern' => '800\\d{7}',
        'example' => '8001234567',
    ],
    'premiumRate' => [
        'pattern' => '90[01]\\d{7}',
        'example' => '9001234567',
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
        'pattern' => '501\\d{7}',
        'example' => '5010123456',
        'posLengthLocal' => [
            7,
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
            'pattern' => '(\\d{3})(\\d{7})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '[24-689]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
    ],
];
