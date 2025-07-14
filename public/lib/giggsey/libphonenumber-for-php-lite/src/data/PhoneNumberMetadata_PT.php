<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'PT',
    'countryCode' => 351,
    'generalDesc' => [
        'pattern' => '1693\\d{5}|(?:[26-9]\\d|30)\\d{7}',
        'posLength' => [
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2(?:[12]\\d|3[1-689]|4[1-59]|[57][1-9]|6[1-35689]|8[1-69]|9[1256])\\d{6}',
        'example' => '212345678',
    ],
    'mobile' => [
        'pattern' => '6(?:[06]92(?:30|9\\d)|[35]92(?:[049]\\d|3[034]))\\d{3}|(?:(?:16|6[0356])93|9(?:[1-36]\\d\\d|480))\\d{5}',
        'example' => '912345678',
    ],
    'tollFree' => [
        'pattern' => '80[02]\\d{6}',
        'example' => '800123456',
    ],
    'premiumRate' => [
        'pattern' => '(?:6(?:0[178]|4[68])\\d|76(?:0[1-57]|1[2-47]|2[237]))\\d{5}',
        'example' => '760123456',
    ],
    'sharedCost' => [
        'pattern' => '80(?:8\\d|9[1579])\\d{5}',
        'example' => '808123456',
    ],
    'personalNumber' => [
        'pattern' => '884[0-4689]\\d{5}',
        'example' => '884123456',
    ],
    'voip' => [
        'pattern' => '30\\d{7}',
        'example' => '301234567',
    ],
    'pager' => [
        'pattern' => '6(?:222\\d|8988)\\d{4}',
        'example' => '622212345',
    ],
    'uan' => [
        'pattern' => '70(?:38[01]|596|(?:7\\d|8[17])\\d)\\d{4}',
        'example' => '707123456',
    ],
    'voicemail' => [
        'pattern' => '600\\d{6}|6[06]92(?:0\\d|3[349]|49)\\d{3}',
        'example' => '600110000',
    ],
    'noInternationalDialling' => [
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '00',
    'numberFormat' => [
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '2[12]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '16|[236-9]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
