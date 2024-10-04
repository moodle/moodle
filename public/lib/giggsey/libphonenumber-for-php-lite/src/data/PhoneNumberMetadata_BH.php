<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BH',
    'countryCode' => 973,
    'generalDesc' => [
        'pattern' => '[136-9]\\d{7}',
        'posLength' => [
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:1(?:3[1356]|6[0156]|7\\d)\\d|6(?:1[16]\\d|500|6(?:0\\d|3[12]|44|55|7[7-9]|88)|9[69][69])|7(?:[07]\\d\\d|1(?:11|78)))\\d{4}',
        'example' => '17001234',
    ],
    'mobile' => [
        'pattern' => '(?:3(?:[0-79]\\d|8[0-57-9])\\d|6(?:3(?:00|33|6[16])|441|6(?:3[03-9]|[69]\\d|7[0-689])))\\d{4}',
        'example' => '36001234',
    ],
    'tollFree' => [
        'pattern' => '8[02369]\\d{6}',
        'example' => '80123456',
    ],
    'premiumRate' => [
        'pattern' => '(?:87|9[0-8])\\d{6}',
        'example' => '90123456',
    ],
    'sharedCost' => [
        'pattern' => '84\\d{6}',
        'example' => '84123456',
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
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[13679]|8[02-4679]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
