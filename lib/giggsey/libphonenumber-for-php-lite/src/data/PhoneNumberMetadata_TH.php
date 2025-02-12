<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'TH',
    'countryCode' => 66,
    'generalDesc' => [
        'pattern' => '(?:001800|[2-57]|[689]\\d)\\d{7}|1\\d{7,9}',
        'posLength' => [
            8,
            9,
            10,
            13,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:1[0689]|2\\d|3[2-9]|4[2-5]|5[2-6]|7[3-7])\\d{6}',
        'example' => '21234567',
        'posLength' => [
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '67(?:1[0-8]|2[4-7])\\d{5}|(?:14|6[1-6]|[89]\\d)\\d{7}',
        'example' => '812345678',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '(?:001800\\d|1800)\\d{6}',
        'example' => '1800123456',
        'posLength' => [
            10,
            13,
        ],
    ],
    'premiumRate' => [
        'pattern' => '1900\\d{6}',
        'example' => '1900123456',
        'posLength' => [
            10,
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
        'pattern' => '6[08]\\d{7}',
        'example' => '601234567',
        'posLength' => [
            9,
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
    'internationalPrefix' => '00[1-9]',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d)(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '2',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[13-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
