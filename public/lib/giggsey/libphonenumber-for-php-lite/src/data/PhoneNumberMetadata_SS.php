<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SS',
    'countryCode' => 211,
    'generalDesc' => [
        'pattern' => '[19]\\d{8}',
        'posLength' => [
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '1[89]\\d{7}',
        'example' => '181234567',
    ],
    'mobile' => [
        'pattern' => '(?:12|9[1257-9])\\d{7}',
        'example' => '977123456',
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
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[19]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
