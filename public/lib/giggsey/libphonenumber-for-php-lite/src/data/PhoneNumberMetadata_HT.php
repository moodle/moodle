<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'HT',
    'countryCode' => 509,
    'generalDesc' => [
        'pattern' => '(?:[2-489]\\d|55)\\d{6}',
        'posLength' => [
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2(?:2\\d|5[1-5]|81|9[149])\\d{5}',
        'example' => '22453300',
    ],
    'mobile' => [
        'pattern' => '(?:[34]\\d|55)\\d{6}',
        'example' => '34101234',
    ],
    'tollFree' => [
        'pattern' => '8\\d{7}',
        'example' => '80012345',
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
        'pattern' => '9(?:[67][0-4]|8[0-3589]|9\\d)\\d{5}',
        'example' => '98901234',
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
            'pattern' => '(\\d{2})(\\d{2})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[2-589]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
