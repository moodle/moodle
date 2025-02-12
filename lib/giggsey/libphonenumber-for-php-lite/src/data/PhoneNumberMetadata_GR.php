<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GR',
    'countryCode' => 30,
    'generalDesc' => [
        'pattern' => '5005000\\d{3}|8\\d{9,11}|(?:[269]\\d|70)\\d{8}',
        'posLength' => [
            10,
            11,
            12,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2(?:1\\d\\d|2(?:2[1-46-9]|[36][1-8]|4[1-7]|5[1-4]|7[1-5]|[89][1-9])|3(?:1\\d|2[1-57]|[35][1-3]|4[13]|7[1-7]|8[124-6]|9[1-79])|4(?:1\\d|2[1-8]|3[1-4]|4[13-5]|6[1-578]|9[1-5])|5(?:1\\d|[29][1-4]|3[1-5]|4[124]|5[1-6])|6(?:1\\d|[269][1-6]|3[1245]|4[1-7]|5[13-9]|7[14]|8[1-5])|7(?:1\\d|2[1-5]|3[1-6]|4[1-7]|5[1-57]|6[135]|9[125-7])|8(?:1\\d|2[1-5]|[34][1-4]|9[1-57]))\\d{6}',
        'example' => '2123456789',
        'posLength' => [
            10,
        ],
    ],
    'mobile' => [
        'pattern' => '68[57-9]\\d{7}|(?:69|94)\\d{8}',
        'example' => '6912345678',
        'posLength' => [
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{7,9}',
        'example' => '8001234567',
    ],
    'premiumRate' => [
        'pattern' => '90[19]\\d{7}',
        'example' => '9091234567',
        'posLength' => [
            10,
        ],
    ],
    'sharedCost' => [
        'pattern' => '8(?:0[16]|12|[27]5|50)\\d{7}',
        'example' => '8011234567',
        'posLength' => [
            10,
        ],
    ],
    'personalNumber' => [
        'pattern' => '70\\d{8}',
        'example' => '7012345678',
        'posLength' => [
            10,
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
        'pattern' => '5005000\\d{3}',
        'example' => '5005000123',
        'posLength' => [
            10,
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
            'pattern' => '(\\d{2})(\\d{4})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '21|7',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '2(?:2|3[2-57-9]|4[2-469]|5[2-59]|6[2-9]|7[2-69]|8[2-49])|5',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[2689]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3,4})(\\d{5})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
