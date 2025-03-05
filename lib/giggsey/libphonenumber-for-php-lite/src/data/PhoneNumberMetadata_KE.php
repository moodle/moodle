<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'KE',
    'countryCode' => 254,
    'generalDesc' => [
        'pattern' => '(?:[17]\\d\\d|900)\\d{6}|(?:2|80)0\\d{6,7}|[4-6]\\d{6,8}',
        'posLength' => [
            7,
            8,
            9,
            10,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:4[245]|5[1-79]|6[01457-9])\\d{5,7}|(?:4[136]|5[08]|62)\\d{7}|(?:[24]0|66)\\d{6,7}',
        'example' => '202012345',
        'posLength' => [
            7,
            8,
            9,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:1(?:0[0-8]|1[0-7]|2[014]|30)|7\\d\\d)\\d{6}',
        'example' => '712123456',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '800[02-8]\\d{5,6}',
        'example' => '800223456',
        'posLength' => [
            9,
            10,
        ],
    ],
    'premiumRate' => [
        'pattern' => '900[02-9]\\d{5}',
        'example' => '900223456',
        'posLength' => [
            9,
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
    'internationalPrefix' => '000',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{5,7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[24-6]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[17]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[89]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
