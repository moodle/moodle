<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BN',
    'countryCode' => 673,
    'generalDesc' => [
        'pattern' => '[2-578]\\d{6}',
        'posLength' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '22[0-7]\\d{4}|(?:2[013-9]|[34]\\d|5[0-25-9])\\d{5}',
        'example' => '2345678',
    ],
    'mobile' => [
        'pattern' => '(?:22[89]|[78]\\d\\d)\\d{4}',
        'example' => '7123456',
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
        'pattern' => '5[34]\\d{5}',
        'example' => '5345678',
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
                '[2-578]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
