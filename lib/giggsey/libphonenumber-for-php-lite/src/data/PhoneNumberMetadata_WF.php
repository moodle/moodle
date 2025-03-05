<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'WF',
    'countryCode' => 681,
    'generalDesc' => [
        'pattern' => '(?:40|72|8\\d{4})\\d{4}|[89]\\d{5}',
        'posLength' => [
            6,
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '72\\d{4}',
        'example' => '721234',
        'posLength' => [
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:72|8[23])\\d{4}',
        'example' => '821234',
        'posLength' => [
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '80[0-5]\\d{6}',
        'example' => '800012345',
        'posLength' => [
            9,
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
        'pattern' => '9[23]\\d{4}',
        'example' => '921234',
        'posLength' => [
            6,
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
        'pattern' => '[48]0\\d{4}',
        'example' => '401234',
        'posLength' => [
            6,
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
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[47-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
