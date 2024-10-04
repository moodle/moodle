<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'ME',
    'countryCode' => 382,
    'generalDesc' => [
        'pattern' => '(?:20|[3-79]\\d)\\d{6}|80\\d{6,7}',
        'posLength' => [
            8,
            9,
        ],
        'posLengthLocal' => [
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:20[2-8]|3(?:[0-2][2-7]|3[24-7])|4(?:0[2-467]|1[2467])|5(?:0[2467]|1[24-7]|2[2-467]))\\d{5}',
        'example' => '30234567',
        'posLength' => [
            8,
        ],
        'posLengthLocal' => [
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '6(?:[07-9]\\d|3[024]|6[0-25])\\d{5}',
        'example' => '67622901',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '80(?:[0-2578]|9\\d)\\d{5}',
        'example' => '80080002',
    ],
    'premiumRate' => [
        'pattern' => '9(?:4[1568]|5[178])\\d{5}',
        'example' => '94515151',
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
        'pattern' => '78[1-49]\\d{5}',
        'example' => '78108780',
        'posLength' => [
            8,
        ],
    ],
    'pager' => [
        'posLength' => [
            -1,
        ],
    ],
    'uan' => [
        'pattern' => '77[1-9]\\d{5}',
        'example' => '77273012',
        'posLength' => [
            8,
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
            'pattern' => '(\\d{2})(\\d{3})(\\d{3,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[2-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
