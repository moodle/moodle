<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'AW',
    'countryCode' => 297,
    'generalDesc' => [
        'pattern' => '(?:[25-79]\\d\\d|800)\\d{4}',
        'posLength' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '5(?:2\\d|8[1-9])\\d{4}',
        'example' => '5212345',
    ],
    'mobile' => [
        'pattern' => '(?:290|5[69]\\d|6(?:[03]0|22|4[0-2]|[69]\\d)|7(?:[34]\\d|7[07])|9(?:6[45]|9[4-8]))\\d{4}',
        'example' => '5601234',
    ],
    'tollFree' => [
        'pattern' => '800\\d{4}',
        'example' => '8001234',
    ],
    'premiumRate' => [
        'pattern' => '900\\d{4}',
        'example' => '9001234',
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
        'pattern' => '(?:28\\d|501)\\d{4}',
        'example' => '5011234',
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
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[25-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
