<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'TW',
    'countryCode' => 886,
    'generalDesc' => [
        'pattern' => '[2-689]\\d{8}|7\\d{9,10}|[2-8]\\d{7}|2\\d{6}',
        'posLength' => [
            7,
            8,
            9,
            10,
            11,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2[2-8]\\d|370|55[01]|7[1-9])\\d{6}|4(?:(?:0(?:0[1-9]|[2-48]\\d)|1[023]\\d)\\d{4,5}|(?:[239]\\d\\d|4(?:0[56]|12|49))\\d{5})|6(?:[01]\\d{7}|4(?:0[56]|12|24|4[09])\\d{4,5})|8(?:(?:2(?:3\\d|4[0-269]|[578]0|66)|36[24-9]|90\\d\\d)\\d{4}|4(?:0[56]|12|24|4[09])\\d{4,5})|(?:2(?:2(?:0\\d\\d|4(?:0[68]|[249]0|3[0-467]|5[0-25-9]|6[0235689]))|(?:3(?:[09]\\d|1[0-4])|(?:4\\d|5[0-49]|6[0-29]|7[0-5])\\d)\\d)|(?:(?:3[2-9]|5[2-8]|6[0-35-79]|8[7-9])\\d\\d|4(?:2(?:[089]\\d|7[1-9])|(?:3[0-4]|[78]\\d|9[01])\\d))\\d)\\d{3}',
        'example' => '221234567',
        'posLength' => [
            8,
            9,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:40001[0-2]|9[0-8]\\d{4})\\d{3}',
        'example' => '912345678',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '80[0-79]\\d{6}|800\\d{5}',
        'example' => '800123456',
        'posLength' => [
            8,
            9,
        ],
    ],
    'premiumRate' => [
        'pattern' => '20(?:[013-9]\\d\\d|2)\\d{4}',
        'example' => '203123456',
        'posLength' => [
            7,
            9,
        ],
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'pattern' => '99\\d{7}',
        'example' => '990123456',
        'posLength' => [
            9,
        ],
    ],
    'voip' => [
        'pattern' => '7010(?:[0-2679]\\d|3[0-7]|8[0-5])\\d{5}|70\\d{8}',
        'example' => '7012345678',
        'posLength' => [
            10,
            11,
        ],
    ],
    'pager' => [
        'posLength' => [
            -1,
        ],
    ],
    'uan' => [
        'pattern' => '50[0-46-9]\\d{6}',
        'example' => '500123456',
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
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '0(?:0[25-79]|19)',
    'nationalPrefix' => '0',
    'preferredExtnPrefix' => '#',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d)(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '202',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[258]0',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3,4})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[23568]|4(?:0[02-48]|[1-47-9])|7[1-9]',
                '[23568]|4(?:0[2-48]|[1-47-9])|(?:400|7)[1-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[49]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{4,5})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '7',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
