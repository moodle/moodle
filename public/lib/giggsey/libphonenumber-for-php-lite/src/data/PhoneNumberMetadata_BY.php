<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BY',
    'countryCode' => 375,
    'generalDesc' => [
        'pattern' => '(?:[12]\\d|33|44|902)\\d{7}|8(?:0[0-79]\\d{5,7}|[1-7]\\d{9})|8(?:1[0-489]|[5-79]\\d)\\d{7}|8[1-79]\\d{6,7}|8[0-79]\\d{5}|8\\d{5}',
        'posLength' => [
            6,
            7,
            8,
            9,
            10,
            11,
        ],
        'posLengthLocal' => [
            5,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:1(?:5(?:1[1-5]|[24]\\d|6[2-4]|9[1-7])|6(?:[235]\\d|4[1-7])|7\\d\\d)|2(?:1(?:[246]\\d|3[0-35-9]|5[1-9])|2(?:[235]\\d|4[0-8])|3(?:[26]\\d|3[02-79]|4[024-7]|5[03-7])))\\d{5}',
        'example' => '152450911',
        'posLength' => [
            9,
        ],
        'posLengthLocal' => [
            5,
            6,
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '(?:2(?:5[5-79]|9[1-9])|(?:33|44)\\d)\\d{6}',
        'example' => '294911911',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{3,7}|8(?:0[13]|20\\d)\\d{7}',
        'example' => '8011234567',
    ],
    'premiumRate' => [
        'pattern' => '(?:810|902)\\d{7}',
        'example' => '9021234567',
        'posLength' => [
            10,
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
        'pattern' => '249\\d{6}',
        'example' => '249123456',
        'posLength' => [
            9,
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
        'pattern' => '800\\d{3,7}|(?:8(?:0[13]|10|20\\d)|902)\\d{7}',
    ],
    'internationalPrefix' => '810',
    'preferredInternationalPrefix' => '8~10',
    'nationalPrefix' => '8',
    'nationalPrefixForParsing' => '0|80?',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{3})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '800',
            ],
            'nationalPrefixFormattingRule' => '8 $1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{2,4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '800',
            ],
            'nationalPrefixFormattingRule' => '8 $1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{2})(\\d{3})',
            'format' => '$1 $2-$3',
            'leadingDigitsPatterns' => [
                '1(?:5[169]|6[3-5]|7[179])|2(?:1[35]|2[34]|3[3-5])',
                '1(?:5[169]|6(?:3[1-3]|4|5[125])|7(?:1[3-9]|7[0-24-6]|9[2-7]))|2(?:1[35]|2[34]|3[3-5])',
            ],
            'nationalPrefixFormattingRule' => '8 0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2-$3-$4',
            'leadingDigitsPatterns' => [
                '1(?:[56]|7[467])|2[1-3]',
            ],
            'nationalPrefixFormattingRule' => '8 0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{2})(\\d{2})',
            'format' => '$1 $2-$3-$4',
            'leadingDigitsPatterns' => [
                '[1-4]',
            ],
            'nationalPrefixFormattingRule' => '8 0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3,4})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[89]',
            ],
            'nationalPrefixFormattingRule' => '8 $1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
