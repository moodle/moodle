<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MG',
    'countryCode' => 261,
    'generalDesc' => [
        'pattern' => '[23]\\d{8}',
        'posLength' => [
            9,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2072[29]\\d{4}|20(?:2\\d|4[47]|5[3467]|6[279]|7[356]|8[268]|9[2457])\\d{5}',
        'example' => '202123456',
        'posLengthLocal' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '3[2-47-9]\\d{7}',
        'example' => '321234567',
    ],
    'tollFree' => [
        'posLength' => [
            -1,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
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
        'pattern' => '22\\d{7}',
        'example' => '221234567',
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
    'nationalPrefixForParsing' => '([24-9]\\d{6})$|0',
    'nationalPrefixTransformRule' => '20$1',
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{2})(\\d{3})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[23]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
