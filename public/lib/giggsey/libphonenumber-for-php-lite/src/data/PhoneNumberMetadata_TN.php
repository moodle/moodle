<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'TN',
    'countryCode' => 216,
    'generalDesc' => [
        'pattern' => '[2-57-9]\\d{7}',
        'posLength' => [
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '81200\\d{3}|(?:3[0-2]|7\\d)\\d{6}',
        'example' => '30010123',
    ],
    'mobile' => [
        'pattern' => '3(?:001|[12]40)\\d{4}|(?:(?:[259]\\d|4[0-8])\\d|3(?:1[1-35]|6[0-4]|91))\\d{5}',
        'example' => '20123456',
    ],
    'tollFree' => [
        'pattern' => '8010\\d{4}',
        'example' => '80101234',
    ],
    'premiumRate' => [
        'pattern' => '88\\d{6}',
        'example' => '88123456',
    ],
    'sharedCost' => [
        'pattern' => '8[12]10\\d{4}',
        'example' => '81101234',
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
            'pattern' => '(\\d{2})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[2-57-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
