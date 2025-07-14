<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'AL',
    'countryCode' => 355,
    'generalDesc' => [
        'pattern' => '(?:700\\d\\d|900)\\d{3}|8\\d{5,7}|(?:[2-5]|6\\d)\\d{7}',
        'posLength' => [
            6,
            7,
            8,
            9,
        ],
        'posLengthLocal' => [
            5,
        ],
    ],
    'fixedLine' => [
        'pattern' => '4505[0-2]\\d{3}|(?:[2358][16-9]\\d[2-9]|4410)\\d{4}|(?:[2358][2-5][2-9]|4(?:[2-57-9][2-9]|6\\d))\\d{5}',
        'example' => '22345678',
        'posLength' => [
            8,
        ],
        'posLengthLocal' => [
            5,
            6,
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '6(?:[78][2-9]|9\\d)\\d{6}',
        'example' => '672123456',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{4}',
        'example' => '8001234',
        'posLength' => [
            7,
        ],
    ],
    'premiumRate' => [
        'pattern' => '900[1-9]\\d\\d',
        'example' => '900123',
        'posLength' => [
            6,
        ],
    ],
    'sharedCost' => [
        'pattern' => '808[1-9]\\d\\d',
        'example' => '808123',
        'posLength' => [
            6,
        ],
    ],
    'personalNumber' => [
        'pattern' => '700[2-9]\\d{4}',
        'example' => '70021234',
        'posLength' => [
            8,
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
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{3,4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '80|9',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '4[2-6]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[2358][2-5]|4',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[23578]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '6',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
