<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'HU',
    'countryCode' => 36,
    'generalDesc' => [
        'pattern' => '[235-7]\\d{8}|[1-9]\\d{7}',
        'posLength' => [
            8,
            9,
        ],
        'posLengthLocal' => [
            6,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:1\\d|[27][2-9]|3[2-7]|4[24-9]|5[2-79]|6[23689]|8[2-57-9]|9[2-69])\\d{6}',
        'example' => '12345678',
        'posLength' => [
            8,
        ],
        'posLengthLocal' => [
            6,
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:[257]0|3[01])\\d{7}',
        'example' => '201234567',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '(?:[48]0\\d|680[29])\\d{5}',
        'example' => '80123456',
    ],
    'premiumRate' => [
        'pattern' => '9[01]\\d{6}',
        'example' => '90123456',
        'posLength' => [
            8,
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
        'pattern' => '21\\d{7}',
        'example' => '211234567',
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
        'pattern' => '38\\d{7}',
        'example' => '381234567',
        'posLength' => [
            9,
        ],
    ],
    'voicemail' => [
        'posLength' => [
            -1,
        ],
    ],
    'noInternationalDialling' => [
        'pattern' => '(?:[48]0\\d|680[29])\\d{5}',
    ],
    'internationalPrefix' => '00',
    'nationalPrefix' => '06',
    'nationalPrefixForParsing' => '06',
    'numberFormat' => [
        [
            'pattern' => '(\\d)(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '(06 $1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[27][2-9]|3[2-7]|4[24-9]|5[2-79]|6|8[2-57-9]|9[2-69]',
            ],
            'nationalPrefixFormattingRule' => '(06 $1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[2-9]',
            ],
            'nationalPrefixFormattingRule' => '06 $1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
