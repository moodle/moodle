<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GQ',
    'countryCode' => 240,
    'generalDesc' => [
        'pattern' => '222\\d{6}|(?:3\\d|55|[89]0)\\d{7}',
        'posLength' => [
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '33[0-24-9]\\d[46]\\d{4}|3(?:33|5\\d)\\d[7-9]\\d{4}',
        'example' => '333091234',
    ],
    'mobile' => [
        'pattern' => '(?:222|55\\d)\\d{6}',
        'example' => '222123456',
    ],
    'tollFree' => [
        'pattern' => '80\\d[1-9]\\d{5}',
        'example' => '800123456',
    ],
    'premiumRate' => [
        'pattern' => '90\\d[1-9]\\d{5}',
        'example' => '900123456',
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
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[235]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[89]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
