<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'DZ',
    'countryCode' => 213,
    'generalDesc' => [
        'pattern' => '(?:[1-4]|[5-79]\\d|80)\\d{7}',
        'posLength' => [
            8,
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '9619\\d{5}|(?:1\\d|2[013-79]|3[0-8]|4[013-689])\\d{6}',
        'example' => '12345678',
    ],
    'mobile' => [
        'pattern' => '(?:5(?:4[0-29]|5\\d|6[0-3])|6(?:[569]\\d|7[0-6])|7[7-9]\\d)\\d{6}',
        'example' => '551234567',
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
        'pattern' => '80[3-689]1\\d{5}',
        'example' => '808123456',
        'posLength' => [
            9,
        ],
    ],
    'sharedCost' => [
        'pattern' => '80[12]1\\d{5}',
        'example' => '801123456',
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
        'pattern' => '98[23]\\d{6}',
        'example' => '983123456',
        'posLength' => [
            9,
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
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[1-4]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '9',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[5-8]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
