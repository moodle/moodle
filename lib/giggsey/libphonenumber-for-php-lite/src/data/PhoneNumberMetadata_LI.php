<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'LI',
    'countryCode' => 423,
    'generalDesc' => [
        'pattern' => '[68]\\d{8}|(?:[2378]\\d|90)\\d{5}',
        'posLength' => [
            7,
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2(?:01|1[27]|2[02]|3\\d|6[02-578]|96)|3(?:[24]0|33|7[0135-7]|8[048]|9[0269]))\\d{4}',
        'example' => '2345678',
        'posLength' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:6(?:(?:4[5-9]|5[0-469])\\d|6(?:[024-6]\\d|[17]0|3[7-9]))\\d|7(?:[37-9]\\d|42|56))\\d{4}',
        'example' => '660234567',
    ],
    'tollFree' => [
        'pattern' => '8002[28]\\d\\d|80(?:05\\d|9)\\d{4}',
        'example' => '8002222',
    ],
    'premiumRate' => [
        'pattern' => '90(?:02[258]|1(?:23|3[14])|66[136])\\d\\d',
        'example' => '9002222',
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
        'pattern' => '870(?:28|87)\\d\\d',
        'example' => '8702812',
        'posLength' => [
            7,
        ],
    ],
    'voicemail' => [
        'pattern' => '697(?:42|56|[78]\\d)\\d{4}',
        'example' => '697861234',
        'posLength' => [
            9,
        ],
    ],
    'noInternationalDialling' => [
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '00',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '(1001)|0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[2379]|8(?:0[09]|7)',
                '[2379]|8(?:0(?:02|9)|7)',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '69',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '6',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '$CC $1',
        ],
    ],
];
