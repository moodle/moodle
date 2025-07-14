<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'QA',
    'countryCode' => 974,
    'generalDesc' => [
        'pattern' => '800\\d{4}|(?:2|800)\\d{6}|(?:0080|[3-7])\\d{7}',
        'posLength' => [
            7,
            8,
            9,
            11,
        ],
    ],
    'fixedLine' => [
        'pattern' => '4(?:1111|2022)\\d{3}|4(?:[04]\\d\\d|14[0-6]|999)\\d{4}',
        'example' => '44123456',
        'posLength' => [
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '[35-7]\\d{7}',
        'example' => '33123456',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{4}|(?:0080[01]|800)\\d{6}',
        'example' => '8001234',
        'posLength' => [
            7,
            9,
            11,
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
        'pattern' => '2[16]\\d{5}',
        'example' => '2123456',
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
    'internationalPrefix' => '00',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '2[16]|8',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[3-7]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
