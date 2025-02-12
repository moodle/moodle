<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'AM',
    'countryCode' => 374,
    'generalDesc' => [
        'pattern' => '(?:[1-489]\\d|55|60|77)\\d{6}',
        'posLength' => [
            8,
        ],
        'posLengthLocal' => [
            5,
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:(?:1[0-25]|47)\\d|2(?:2[2-46]|3[1-8]|4[2-69]|5[2-7]|6[1-9]|8[1-7])|3[12]2)\\d{5}',
        'example' => '10123456',
        'posLengthLocal' => [
            5,
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:33|4[1349]|55|77|88|9[13-9])\\d{6}',
        'example' => '77123456',
    ],
    'tollFree' => [
        'pattern' => '800\\d{5}',
        'example' => '80012345',
    ],
    'premiumRate' => [
        'pattern' => '90[016]\\d{5}',
        'example' => '90012345',
    ],
    'sharedCost' => [
        'pattern' => '80[1-4]\\d{5}',
        'example' => '80112345',
    ],
    'personalNumber' => [
        'posLength' => [
            -1,
        ],
    ],
    'voip' => [
        'pattern' => '60(?:2[78]|3[5-9]|4[02-9]|5[0-46-9]|[6-8]\\d|9[0-2])\\d{4}',
        'example' => '60271234',
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
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[89]0',
            ],
            'nationalPrefixFormattingRule' => '0 $1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '2|3[12]',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1|47',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[3-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
