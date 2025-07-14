<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'RS',
    'countryCode' => 381,
    'generalDesc' => [
        'pattern' => '38[02-9]\\d{6,9}|6\\d{7,9}|90\\d{4,8}|38\\d{5,6}|(?:7\\d\\d|800)\\d{3,9}|(?:[12]\\d|3[0-79])\\d{5,10}',
        'posLength' => [
            6,
            7,
            8,
            9,
            10,
            11,
            12,
        ],
        'posLengthLocal' => [
            4,
            5,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:11[1-9]\\d|(?:2[389]|39)(?:0[2-9]|[2-9]\\d))\\d{3,8}|(?:1[02-9]|2[0-24-7]|3[0-8])[2-9]\\d{4,9}',
        'example' => '10234567',
        'posLength' => [
            7,
            8,
            9,
            10,
            11,
            12,
        ],
        'posLengthLocal' => [
            4,
            5,
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '6(?:[0-689]|7\\d)\\d{6,7}',
        'example' => '601234567',
        'posLength' => [
            8,
            9,
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{3,9}',
        'example' => '80012345',
    ],
    'premiumRate' => [
        'pattern' => '(?:78\\d|90[0169])\\d{3,7}',
        'example' => '90012345',
        'posLength' => [
            6,
            7,
            8,
            9,
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
        'pattern' => '7[06]\\d{4,10}',
        'example' => '700123456',
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
            'pattern' => '(\\d{3})(\\d{3,9})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '(?:2[389]|39)0|[7-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{5,10})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[1-36]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
