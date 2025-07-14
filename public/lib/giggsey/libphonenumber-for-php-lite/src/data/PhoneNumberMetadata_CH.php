<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CH',
    'countryCode' => 41,
    'generalDesc' => [
        'pattern' => '8\\d{11}|[2-9]\\d{8}',
        'posLength' => [
            9,
            12,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2[12467]|3[1-4]|4[134]|5[256]|6[12]|[7-9]1)\\d{7}',
        'example' => '212345678',
        'posLength' => [
            9,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:6[89]|7[235-9])\\d{7}',
        'example' => '781234567',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{6}',
        'example' => '800123456',
        'posLength' => [
            9,
        ],
    ],
    'premiumRate' => [
        'pattern' => '90[016]\\d{6}',
        'example' => '900123456',
        'posLength' => [
            9,
        ],
    ],
    'sharedCost' => [
        'pattern' => '84[0248]\\d{6}',
        'example' => '840123456',
        'posLength' => [
            9,
        ],
    ],
    'personalNumber' => [
        'pattern' => '878\\d{6}',
        'example' => '878123456',
        'posLength' => [
            9,
        ],
    ],
    'voip' => [
        'posLength' => [
            -1,
        ],
    ],
    'pager' => [
        'pattern' => '74[0248]\\d{6}',
        'example' => '740123456',
        'posLength' => [
            9,
        ],
    ],
    'uan' => [
        'pattern' => '5[18]\\d{7}',
        'example' => '581234567',
        'posLength' => [
            9,
        ],
    ],
    'voicemail' => [
        'pattern' => '860\\d{9}',
        'example' => '860123456789',
        'posLength' => [
            12,
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
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '8[047]|90',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[2-79]|81',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4 $5',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
