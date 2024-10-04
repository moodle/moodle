<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MA',
    'countryCode' => 212,
    'generalDesc' => [
        'pattern' => '[5-8]\\d{8}',
        'posLength' => [
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '5(?:2(?:[0-25-79]\\d|3[1-578]|4[02-46-8]|8[0235-7])|3(?:[0-47]\\d|5[02-9]|6[02-8]|8[014-9]|9[3-9])|(?:4[067]|5[03])\\d)\\d{5}',
        'example' => '520123456',
    ],
    'mobile' => [
        'pattern' => '(?:6(?:[0-79]\\d|8[0-247-9])|7(?:[0167]\\d|2[0-467]|5[0-3]|8[0-5]))\\d{6}',
        'example' => '650123456',
    ],
    'tollFree' => [
        'pattern' => '80[0-7]\\d{6}',
        'example' => '801234567',
    ],
    'premiumRate' => [
        'pattern' => '89\\d{7}',
        'example' => '891234567',
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
        'pattern' => '(?:592(?:4[0-2]|93)|80[89]\\d\\d)\\d{4}',
        'example' => '592401234',
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
            'pattern' => '(\\d{3})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '5[45]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{5})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '5(?:2[2-46-9]|3[3-9]|9)|8(?:0[89]|92)',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{7})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{6})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '[5-7]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mainCountryForCode' => true,
    'mobileNumberPortableRegion' => true,
];
