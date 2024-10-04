<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'UY',
    'countryCode' => 598,
    'generalDesc' => [
        'pattern' => '0004\\d{2,9}|[1249]\\d{7}|(?:[49]\\d|80)\\d{5}',
        'posLength' => [
            6,
            7,
            8,
            9,
            10,
            11,
            12,
            13,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:1(?:770|9(?:20|[89]7))|(?:2\\d|4[2-7])\\d\\d)\\d{4}',
        'example' => '21231234',
        'posLength' => [
            8,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '9[1-9]\\d{6}',
        'example' => '94231234',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '0004\\d{2,9}|(?:405|80[05])\\d{4}',
        'example' => '8001234',
    ],
    'premiumRate' => [
        'pattern' => '90[0-8]\\d{4}',
        'example' => '9001234',
        'posLength' => [
            7,
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
    'internationalPrefix' => '0(?:0|1[3-9]\\d)',
    'preferredInternationalPrefix' => '00',
    'nationalPrefix' => '0',
    'preferredExtnPrefix' => ' int. ',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{3,4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '0',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[49]0|8',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '9',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[124]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{2,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '0',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})(\\d{2,4})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '0',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
