<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GY',
    'countryCode' => 592,
    'generalDesc' => [
        'pattern' => '(?:[2-8]\\d{3}|9008)\\d{3}',
        'posLength' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2(?:1[6-9]|2[0-35-9]|3[1-4]|5[3-9]|6\\d|7[0-79])|3(?:2[25-9]|3\\d)|4(?:4[0-24]|5[56])|50[0-6]|77[1-57])\\d{4}',
        'example' => '2201234',
    ],
    'mobile' => [
        'pattern' => '510\\d{4}|(?:6\\d|7[0-5])\\d{5}',
        'example' => '6091234',
    ],
    'tollFree' => [
        'pattern' => '(?:289|8(?:00|6[28]|88|99))\\d{4}',
        'example' => '2891234',
    ],
    'premiumRate' => [
        'pattern' => '9008\\d{3}',
        'example' => '9008123',
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
        'pattern' => '515\\d{4}',
        'example' => '5151234',
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
    'internationalPrefix' => '001',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[2-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
