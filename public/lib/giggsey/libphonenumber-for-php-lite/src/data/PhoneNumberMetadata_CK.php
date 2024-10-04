<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CK',
    'countryCode' => 682,
    'generalDesc' => [
        'pattern' => '[2-578]\\d{4}',
        'posLength' => [
            5,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2\\d|3[13-7]|4[1-5])\\d{3}',
        'example' => '21234',
    ],
    'mobile' => [
        'pattern' => '[578]\\d{4}',
        'example' => '71234',
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
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{3})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[2-578]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
