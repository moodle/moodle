<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'JO',
    'countryCode' => 962,
    'generalDesc' => [
        'pattern' => '(?:(?:[2689]|7\\d)\\d|32|53)\\d{6}',
        'posLength' => [
            8,
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '87(?:000|90[01])\\d{3}|(?:2(?:6(?:2[0-35-9]|3[0-578]|4[24-7]|5[0-24-8]|[6-8][023]|9[0-3])|7(?:0[1-79]|10|2[014-7]|3[0-689]|4[019]|5[0-3578]))|32(?:0[1-69]|1[1-35-7]|2[024-7]|3\\d|4[0-3]|[5-7][023])|53(?:0[0-3]|[13][023]|2[0-59]|49|5[0-35-9]|6[15]|7[45]|8[1-6]|9[0-36-9])|6(?:2(?:[05]0|22)|3(?:00|33)|4(?:0[0-25]|1[2-7]|2[0569]|[38][07-9]|4[025689]|6[0-589]|7\\d|9[0-2])|5(?:[01][056]|2[034]|3[0-57-9]|4[178]|5[0-69]|6[0-35-9]|7[1-379]|8[0-68]|9[0239]))|87(?:20|7[078]|99))\\d{4}',
        'example' => '62001234',
        'posLength' => [
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '7(?:[78][0-25-9]|9\\d)\\d{6}',
        'example' => '790123456',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '80\\d{6}',
        'example' => '80012345',
        'posLength' => [
            8,
        ],
    ],
    'premiumRate' => [
        'pattern' => '9\\d{7}',
        'example' => '90012345',
        'posLength' => [
            8,
        ],
    ],
    'sharedCost' => [
        'pattern' => '85\\d{6}',
        'example' => '85012345',
        'posLength' => [
            8,
        ],
    ],
    'personalNumber' => [
        'pattern' => '70\\d{7}',
        'example' => '700123456',
        'posLength' => [
            9,
        ],
    ],
    'voip' => [
        'posLength' => [
            -1,
        ],
    ],
    'pager' => [
        'pattern' => '74(?:66|77)\\d{5}',
        'example' => '746612345',
        'posLength' => [
            9,
        ],
    ],
    'uan' => [
        'pattern' => '8(?:10|8\\d)\\d{5}',
        'example' => '88101234',
        'posLength' => [
            8,
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
            'pattern' => '(\\d)(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[2356]|87',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{5,6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[89]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '70',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{4})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '7',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
