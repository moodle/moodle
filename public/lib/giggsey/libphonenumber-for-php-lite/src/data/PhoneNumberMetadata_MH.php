<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MH',
    'countryCode' => 692,
    'generalDesc' => [
        'pattern' => '329\\d{4}|(?:[256]\\d|45)\\d{5}',
        'posLength' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:247|528|625)\\d{4}',
        'example' => '2471234',
    ],
    'mobile' => [
        'pattern' => '(?:(?:23|54)5|329|45[35-8])\\d{4}',
        'example' => '2351234',
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
        'pattern' => '635\\d{4}',
        'example' => '6351234',
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
    'internationalPrefix' => '011',
    'nationalPrefix' => '1',
    'nationalPrefixForParsing' => '1',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '[2-6]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
