<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'LT',
    'countryCode' => 370,
    'generalDesc' => [
        'pattern' => '(?:[3469]\\d|52|[78]0)\\d{6}',
        'posLength' => [
            8,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:3[1478]|4[124-6]|52)\\d{6}',
        'example' => '31234567',
    ],
    'mobile' => [
        'pattern' => '6\\d{7}',
        'example' => '61234567',
    ],
    'tollFree' => [
        'pattern' => '80[02]\\d{5}',
        'example' => '80012345',
    ],
    'premiumRate' => [
        'pattern' => '9(?:0[0239]|10)\\d{5}',
        'example' => '90012345',
    ],
    'sharedCost' => [
        'pattern' => '808\\d{5}',
        'example' => '80812345',
    ],
    'personalNumber' => [
        'pattern' => '70[05]\\d{5}',
        'example' => '70012345',
    ],
    'voip' => [
        'pattern' => '[89]01\\d{5}',
        'example' => '80123456',
    ],
    'pager' => [
        'posLength' => [
            -1,
        ],
    ],
    'uan' => [
        'pattern' => '70[67]\\d{5}',
        'example' => '70712345',
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
    'nationalPrefixForParsing' => '[08]',
    'numberFormat' => [
        [
            'pattern' => '(\\d)(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '52[0-7]',
            ],
            'nationalPrefixFormattingRule' => '(0-$1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[7-9]',
            ],
            'nationalPrefixFormattingRule' => '0 $1',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{2})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '37|4(?:[15]|6[1-8])',
            ],
            'nationalPrefixFormattingRule' => '(0-$1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
        [
            'pattern' => '(\\d{3})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[3-6]',
            ],
            'nationalPrefixFormattingRule' => '(0-$1)',
            'domesticCarrierCodeFormattingRule' => '',
            'nationalPrefixOptionalWhenFormatting' => true,
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
