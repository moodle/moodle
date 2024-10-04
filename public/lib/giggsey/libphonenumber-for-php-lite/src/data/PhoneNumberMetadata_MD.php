<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MD',
    'countryCode' => 373,
    'generalDesc' => [
        'pattern' => '(?:[235-7]\\d|[89]0)\\d{6}',
        'posLength' => [
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:(?:2[1-9]|3[1-79])\\d|5(?:33|5[257]))\\d{5}',
        'example' => '22212345',
    ],
    'mobile' => [
        'pattern' => '562\\d{5}|(?:6\\d|7[16-9])\\d{6}',
        'example' => '62112345',
    ],
    'tollFree' => [
        'pattern' => '800\\d{5}',
        'example' => '80012345',
    ],
    'premiumRate' => [
        'pattern' => '90[056]\\d{5}',
        'example' => '90012345',
    ],
    'sharedCost' => [
        'pattern' => '808\\d{5}',
        'example' => '80812345',
    ],
    'personalNumber' => [
        'posLength' => [
            -1,
        ],
    ],
    'voip' => [
        'pattern' => '3[08]\\d{6}',
        'example' => '30123456',
    ],
    'pager' => [
        'posLength' => [
            -1,
        ],
    ],
    'uan' => [
        'pattern' => '803\\d{5}',
        'example' => '80312345',
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
            'pattern' => '(\\d{3})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[89]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '22|3',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[25-7]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
