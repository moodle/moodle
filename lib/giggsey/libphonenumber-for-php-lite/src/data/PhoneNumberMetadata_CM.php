<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CM',
    'countryCode' => 237,
    'generalDesc' => [
        'pattern' => '[26]\\d{8}|88\\d{6,7}',
        'posLength' => [
            8,
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2(?:22|33)\\d{6}',
        'example' => '222123456',
        'posLength' => [
            9,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:24[23]|6(?:[25-9]\\d|40))\\d{6}',
        'example' => '671234567',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '88\\d{6,7}',
        'example' => '88012345',
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
            'pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '88',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4 $5',
            'leadingDigitsPatterns' => [
                '[26]|88',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
