<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'ID',
    'countryCode' => 62,
    'generalDesc' => [
        'pattern' => '00[1-9]\\d{9,14}|(?:[1-36]|8\\d{5})\\d{6}|00\\d{9}|[1-9]\\d{8,10}|[2-9]\\d{7}',
        'posLength' => [
            7,
            8,
            9,
            10,
            11,
            12,
            13,
            14,
            15,
            16,
            17,
        ],
        'posLengthLocal' => [
            5,
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '2[124]\\d{7,8}|619\\d{8}|2(?:1(?:14|500)|2\\d{3})\\d{3}|61\\d{5,8}|(?:2(?:[35][1-4]|6[0-8]|7[1-6]|8\\d|9[1-8])|3(?:1|[25][1-8]|3[1-68]|4[1-3]|6[1-3568]|7[0-469]|8\\d)|4(?:0[1-589]|1[01347-9]|2[0-36-8]|3[0-24-68]|43|5[1-378]|6[1-5]|7[134]|8[1245])|5(?:1[1-35-9]|2[25-8]|3[124-9]|4[1-3589]|5[1-46]|6[1-8])|6(?:[25]\\d|3[1-69]|4[1-6])|7(?:02|[125][1-9]|[36]\\d|4[1-8]|7[0-36-9])|9(?:0[12]|1[013-8]|2[0-479]|5[125-8]|6[23679]|7[159]|8[01346]))\\d{5,8}',
        'example' => '218350123',
        'posLength' => [
            7,
            8,
            9,
            10,
            11,
        ],
        'posLengthLocal' => [
            5,
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '8[1-35-9]\\d{7,10}',
        'example' => '812345678',
        'posLength' => [
            9,
            10,
            11,
            12,
        ],
    ],
    'tollFree' => [
        'pattern' => '00(?:1803\\d{5,11}|7803\\d{7})|(?:177\\d|800)\\d{5,7}',
        'example' => '8001234567',
        'posLength' => [
            8,
            9,
            10,
            11,
            12,
            13,
            14,
            15,
            16,
            17,
        ],
    ],
    'premiumRate' => [
        'pattern' => '809\\d{7}',
        'example' => '8091234567',
        'posLength' => [
            10,
        ],
    ],
    'sharedCost' => [
        'pattern' => '804\\d{7}',
        'example' => '8041234567',
        'posLength' => [
            10,
        ],
    ],
    'personalNumber' => [
        'posLength' => [
            -1,
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
        'pattern' => '(?:1500|8071\\d{3})\\d{3}',
        'example' => '8071123456',
        'posLength' => [
            7,
            10,
        ],
    ],
    'voicemail' => [
        'posLength' => [
            -1,
        ],
    ],
    'noInternationalDialling' => [
        'pattern' => '001803\\d{5,11}|(?:007803\\d|8071)\\d{6}',
        'posLength' => [
            10,
            11,
            12,
            13,
            14,
            15,
            16,
            17,
        ],
    ],
    'internationalPrefix' => '00[89]',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d)(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '15',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{5,9})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '2[124]|[36]1',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{5,7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '800',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{5,8})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[2-79]',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3,4})(\\d{3})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '8[1-35-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{6,8})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '804',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d)(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '80',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{4})(\\d{4,5})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})(\\d{2,8})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '001',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '0',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'intlNumberFormat' => [
        [
            'pattern' => '(\\d)(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '15',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{5,9})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '2[124]|[36]1',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{5,7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '800',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{5,8})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[2-79]',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3,4})(\\d{3})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '8[1-35-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{6,8})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '804',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d)(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '80',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{4})(\\d{4,5})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
];
