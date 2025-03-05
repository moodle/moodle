<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'OM',
    'countryCode' => 968,
    'generalDesc' => [
        'pattern' => '(?:1505|[279]\\d{3}|500)\\d{4}|800\\d{5,6}',
        'posLength' => [
            7,
            8,
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2[1-6]\\d{6}',
        'example' => '23123456',
        'posLength' => [
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:1505|90[1-9]\\d)\\d{4}|(?:7[126-9]|9[1-9])\\d{6}',
        'example' => '92123456',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '8007\\d{4,5}|(?:500|800[05])\\d{4}',
        'example' => '80071234',
    ],
    'premiumRate' => [
        'pattern' => '900\\d{5}',
        'example' => '90012345',
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
            'pattern' => '(\\d{3})(\\d{4,6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[58]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '2',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[179]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
