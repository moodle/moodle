<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CY',
    'countryCode' => 357,
    'generalDesc' => [
        'pattern' => '(?:[279]\\d|[58]0)\\d{6}',
        'posLength' => [
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2[2-6]\\d{6}',
        'example' => '22345678',
    ],
    'mobile' => [
        'pattern' => '9(?:10|[4-79]\\d)\\d{5}',
        'example' => '96123456',
    ],
    'tollFree' => [
        'pattern' => '800\\d{5}',
        'example' => '80001234',
    ],
    'premiumRate' => [
        'pattern' => '90[09]\\d{5}',
        'example' => '90012345',
    ],
    'sharedCost' => [
        'pattern' => '80[1-9]\\d{5}',
        'example' => '80112345',
    ],
    'personalNumber' => [
        'pattern' => '700\\d{5}',
        'example' => '70012345',
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
        'pattern' => '(?:50|77)\\d{6}',
        'example' => '77123456',
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
            'pattern' => '(\\d{2})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[257-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
