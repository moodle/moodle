<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MU',
    'countryCode' => 230,
    'generalDesc' => [
        'pattern' => '(?:[57]|8\\d\\d)\\d{7}|[2-468]\\d{6}',
        'posLength' => [
            7,
            8,
            10,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2(?:[0346-8]\\d|1[0-7])|4(?:[013568]\\d|2[4-8]|71|90)|54(?:[3-5]\\d|71)|6\\d\\d|8(?:14|3[129]))\\d{4}',
        'example' => '54480123',
        'posLength' => [
            7,
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '5(?:4(?:2[1-389]|7[1-9])|87[15-8])\\d{4}|(?:5(?:2[5-9]|4[3-689]|[57]\\d|8[0-689]|9[0-8])|7(?:0[0-4]|3[013]))\\d{5}',
        'example' => '52512345',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '802\\d{7}|80[0-2]\\d{4}',
        'example' => '8001234',
        'posLength' => [
            7,
            10,
        ],
    ],
    'premiumRate' => [
        'pattern' => '30\\d{5}',
        'example' => '3012345',
        'posLength' => [
            7,
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
        'pattern' => '3(?:20|9\\d)\\d{4}',
        'example' => '3201234',
        'posLength' => [
            7,
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
    'internationalPrefix' => '0(?:0|[24-7]0|3[03])',
    'preferredInternationalPrefix' => '020',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[2-46]|8[013]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[57]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{5})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
