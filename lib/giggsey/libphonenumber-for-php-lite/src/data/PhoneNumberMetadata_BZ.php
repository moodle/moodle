<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BZ',
    'countryCode' => 501,
    'generalDesc' => [
        'pattern' => '(?:0800\\d|[2-8])\\d{6}',
        'posLength' => [
            7,
            11,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2(?:[02]\\d|36|[68]0)|[3-58](?:[02]\\d|[68]0)|7(?:[02]\\d|32|[68]0))\\d{4}',
        'example' => '2221234',
        'posLength' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '6[0-35-7]\\d{5}',
        'example' => '6221234',
        'posLength' => [
            7,
        ],
    ],
    'tollFree' => [
        'pattern' => '0800\\d{7}',
        'example' => '08001234123',
        'posLength' => [
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
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '[2-8]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3})(\\d{4})(\\d{3})',
            'format' => '$1-$2-$3-$4',
            'leadingDigitsPatterns' => [
                '0',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
