<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SK',
    'countryCode' => 421,
    'generalDesc' => [
        'pattern' => '[2-689]\\d{8}|[2-59]\\d{6}|[2-5]\\d{5}',
        'posLength' => [
            6,
            7,
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2(?:16|[2-9]\\d{3})|(?:(?:[3-5][1-8]\\d|819)\\d|601[1-5])\\d)\\d{4}|(?:2|[3-5][1-8])1[67]\\d{3}|[3-5][1-8]16\\d\\d',
        'example' => '221234567',
    ],
    'mobile' => [
        'pattern' => '909[1-9]\\d{5}|9(?:0[1-8]|1[0-24-9]|4[03-57-9]|5\\d)\\d{6}',
        'example' => '912123456',
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
        'pattern' => '9(?:00|[78]\\d)\\d{6}',
        'example' => '900123456',
        'posLength' => [
            9,
        ],
    ],
    'sharedCost' => [
        'pattern' => '8[5-9]\\d{7}',
        'example' => '850123456',
        'posLength' => [
            9,
        ],
    ],
    'personalNumber' => [
        'posLength' => [
            -1,
        ],
    ],
    'voip' => [
        'pattern' => '6(?:02|5[0-4]|9[0-6])\\d{6}',
        'example' => '690123456',
        'posLength' => [
            9,
        ],
    ],
    'pager' => [
        'pattern' => '9090\\d{3}',
        'example' => '9090123',
        'posLength' => [
            7,
        ],
    ],
    'uan' => [
        'pattern' => '96\\d{7}',
        'example' => '961234567',
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
        'pattern' => '9090\\d{3}|(?:602|8(?:00|[5-9]\\d)|9(?:00|[78]\\d))\\d{6}',
        'posLength' => [
            7,
            9,
        ],
    ],
    'internationalPrefix' => '00',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d)(\\d{2})(\\d{3,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '21',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2,3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[3-5][1-8]1',
                '[3-5][1-8]1[67]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{3})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '909',
                '9090',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3})(\\d{3})(\\d{2})',
            'format' => '$1/$2 $3 $4',
            'leadingDigitsPatterns' => [
                '2',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[689]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1/$2 $3 $4',
            'leadingDigitsPatterns' => [
                '[3-5]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'intlNumberFormat' => [
        [
            'pattern' => '(\\d)(\\d{2})(\\d{3,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '21',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{2,3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[3-5][1-8]1',
                '[3-5][1-8]1[67]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3})(\\d{3})(\\d{2})',
            'format' => '$1/$2 $3 $4',
            'leadingDigitsPatterns' => [
                '2',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[689]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1/$2 $3 $4',
            'leadingDigitsPatterns' => [
                '[3-5]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
