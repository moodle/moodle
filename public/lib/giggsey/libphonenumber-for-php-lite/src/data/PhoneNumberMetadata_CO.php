<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CO',
    'countryCode' => 57,
    'generalDesc' => [
        'pattern' => '(?:46|60\\d\\d)\\d{6}|(?:1\\d|[39])\\d{9}',
        'posLength' => [
            8,
            10,
            11,
        ],
        'posLengthLocal' => [
            4,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '601055(?:[0-4]\\d|50)\\d\\d|6010(?:[0-4]\\d|5[0-4])\\d{4}|(?:46|60(?:[124-7][2-9]|8[1-9]))\\d{6}',
        'example' => '6012345678',
        'posLength' => [
            8,
            10,
        ],
        'posLengthLocal' => [
            4,
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '333301[0-5]\\d{3}|3333(?:00|2[5-9]|[3-9]\\d)\\d{4}|(?:3(?:24[1-9]|3(?:00|3[0-24-9]))|9101)\\d{6}|3(?:0[0-5]|1\\d|2[0-3]|5[01]|70)\\d{7}',
        'example' => '3211234567',
        'posLength' => [
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '1800\\d{7}',
        'example' => '18001234567',
        'posLength' => [
            11,
        ],
    ],
    'premiumRate' => [
        'pattern' => '(?:19(?:0[01]|4[78])|901)\\d{7}',
        'example' => '19001234567',
        'posLength' => [
            10,
            11,
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
    'internationalPrefix' => '00(?:4(?:[14]4|56)|[579])',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0([3579]|4(?:[14]4|56))?',
    'numberFormat' => [
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '46',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '6|90',
            ],
            'nationalPrefixFormattingRule' => '($1)',
            'domesticCarrierCodeFormattingRule' => '0$CC $1',
        ],
        [
            'pattern' => '(\\d{3})(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '3[0-357]|91',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '0$CC $1',
        ],
        [
            'pattern' => '(\\d)(\\d{3})(\\d{7})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'intlNumberFormat' => [
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '46',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '6|90',
            ],
            'nationalPrefixFormattingRule' => '($1)',
            'domesticCarrierCodeFormattingRule' => '0$CC $1',
        ],
        [
            'pattern' => '(\\d{3})(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '3[0-357]|91',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '0$CC $1',
        ],
        [
            'pattern' => '(\\d)(\\d{3})(\\d{7})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '1',
            ],
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
