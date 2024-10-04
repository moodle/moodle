<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'PK',
    'countryCode' => 92,
    'generalDesc' => [
        'pattern' => '122\\d{6}|[24-8]\\d{10,11}|9(?:[013-9]\\d{8,10}|2(?:[01]\\d\\d|2(?:[06-8]\\d|1[01]))\\d{7})|(?:[2-8]\\d{3}|92(?:[0-7]\\d|8[1-9]))\\d{6}|[24-9]\\d{8}|[89]\\d{7}',
        'posLength' => [
            8,
            9,
            10,
            11,
            12,
        ],
        'posLengthLocal' => [
            5,
            6,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:(?:21|42)[2-9]|58[126])\\d{7}|(?:2[25]|4[0146-9]|5[1-35-7]|6[1-8]|7[14]|8[16]|91)[2-9]\\d{6,7}|(?:2(?:3[2358]|4[2-4]|9[2-8])|45[3479]|54[2-467]|60[468]|72[236]|8(?:2[2-689]|3[23578]|4[3478]|5[2356])|9(?:2[2-8]|3[27-9]|4[2-6]|6[3569]|9[25-8]))[2-9]\\d{5,6}',
        'example' => '2123456789',
        'posLength' => [
            9,
            10,
        ],
        'posLengthLocal' => [
            5,
            6,
            7,
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '3(?:[0-247]\\d|3[0-79]|55|64)\\d{7}',
        'example' => '3012345678',
        'posLength' => [
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{5}(?:\\d{3})?',
        'example' => '80012345',
        'posLength' => [
            8,
            11,
        ],
    ],
    'premiumRate' => [
        'pattern' => '900\\d{5}',
        'example' => '90012345',
        'posLength' => [
            8,
        ],
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'pattern' => '122\\d{6}',
        'example' => '122044444',
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
        'posLength' => [
            -1,
        ],
    ],
    'uan' => [
        'pattern' => '(?:2(?:[125]|3[2358]|4[2-4]|9[2-8])|4(?:[0-246-9]|5[3479])|5(?:[1-35-7]|4[2-467])|6(?:0[468]|[1-8])|7(?:[14]|2[236])|8(?:[16]|2[2-689]|3[23578]|4[3478]|5[2356])|9(?:1|22|3[27-9]|4[2-6]|6[3569]|9[2-7]))111\\d{6}',
        'example' => '21111825888',
        'posLength' => [
            11,
            12,
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
            'pattern' => '(\\d{3})(\\d{3})(\\d{2,7})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '[89]0',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{4})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{6,7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '2(?:3[2358]|4[2-4]|9[2-8])|45[3479]|54[2-467]|60[468]|72[236]|8(?:2[2-689]|3[23578]|4[3478]|5[2356])|9(?:2[2-8]|3[27-9]|4[2-6]|6[3569]|9[25-8])',
                '9(?:2[3-8]|98)|(?:2(?:3[2358]|4[2-4]|9[2-8])|45[3479]|54[2-467]|60[468]|72[236]|8(?:2[2-689]|3[23578]|4[3478]|5[2356])|9(?:22|3[27-9]|4[2-6]|6[3569]|9[25-7]))[2-9]',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{7,8})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '(?:2[125]|4[0-246-9]|5[1-35-7]|6[1-8]|7[14]|8[16]|91)[2-9]',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{5})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '58',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '3',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '2[125]|4[0-246-9]|5[1-35-7]|6[1-8]|7[14]|8[16]|91',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[24-9]',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
