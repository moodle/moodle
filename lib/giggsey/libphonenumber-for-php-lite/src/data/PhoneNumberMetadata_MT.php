<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MT',
    'countryCode' => 356,
    'generalDesc' => [
        'pattern' => '3550\\d{4}|(?:[2579]\\d\\d|800)\\d{5}',
        'posLength' => [
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '20(?:3[1-4]|6[059])\\d{4}|2(?:0[19]|[1-357]\\d|60)\\d{5}',
        'example' => '21001234',
    ],
    'mobile' => [
        'pattern' => '(?:7(?:210|[79]\\d\\d)|9(?:[29]\\d\\d|69[67]|8(?:1[1-3]|89|97)))\\d{4}',
        'example' => '96961234',
    ],
    'tollFree' => [
        'pattern' => '800(?:02|[3467]\\d)\\d{3}',
        'example' => '80071234',
    ],
    'premiumRate' => [
        'pattern' => '5(?:0(?:0(?:37|43)|(?:6\\d|70|9[0168])\\d)|[12]\\d0[1-5])\\d{3}',
        'example' => '50037123',
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
        'pattern' => '3550\\d{4}',
        'example' => '35501234',
    ],
    'pager' => [
        'pattern' => '7117\\d{4}',
        'example' => '71171234',
    ],
    'uan' => [
        'pattern' => '501\\d{5}',
        'example' => '50112345',
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
                '[2357-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
