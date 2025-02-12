<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'KR',
    'countryCode' => 82,
    'generalDesc' => [
        'pattern' => '00[1-9]\\d{8,11}|(?:[12]|5\\d{3})\\d{7}|[13-6]\\d{9}|(?:[1-6]\\d|80)\\d{7}|[3-6]\\d{4,5}|(?:00|7)0\\d{8}',
        'posLength' => [
            5,
            6,
            8,
            9,
            10,
            11,
            12,
            13,
            14,
        ],
        'posLengthLocal' => [
            3,
            4,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2|3[1-3]|[46][1-4]|5[1-5])[1-9]\\d{6,7}|(?:3[1-3]|[46][1-4]|5[1-5])1\\d{2,3}',
        'example' => '22123456',
        'posLength' => [
            5,
            6,
            8,
            9,
            10,
        ],
        'posLengthLocal' => [
            3,
            4,
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '1(?:05(?:[0-8]\\d|9[0-6])|22[13]\\d)\\d{4,5}|1(?:0[0-46-9]|[16-9]\\d|2[013-9])\\d{6,7}',
        'example' => '1020000000',
        'posLength' => [
            9,
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '00(?:308\\d{6,7}|798\\d{7,9})|(?:00368|[38]0)\\d{7}',
        'example' => '801234567',
        'posLength' => [
            9,
            11,
            12,
            13,
            14,
        ],
    ],
    'premiumRate' => [
        'pattern' => '60[2-9]\\d{6}',
        'example' => '602345678',
        'posLength' => [
            9,
        ],
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'pattern' => '50\\d{8,9}',
        'example' => '5012345678',
        'posLength' => [
            10,
            11,
        ],
    ],
    'voip' => [
        'pattern' => '70\\d{8}',
        'example' => '7012345678',
        'posLength' => [
            10,
        ],
    ],
    'pager' => [
        'pattern' => '15\\d{7,8}',
        'example' => '1523456789',
        'posLength' => [
            9,
            10,
        ],
    ],
    'uan' => [
        'pattern' => '1(?:5(?:22|33|44|66|77|88|99)|6(?:[07]0|44|6[0168]|88)|8(?:00|33|55|77|99))\\d{4}',
        'example' => '15441234',
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
        'pattern' => '00(?:3(?:08\\d{6,7}|68\\d{7})|798\\d{7,9})',
        'posLength' => [
            11,
            12,
            13,
            14,
        ],
    ],
    'internationalPrefix' => '00(?:[125689]|3(?:[46]5|91)|7(?:00|27|3|55|6[126]))',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0(8(?:[1-46-8]|5\\d\\d))?',
    'numberFormat' => [
        [
            'pattern' => '(\\d{5})',
            'format' => '$1',
            'leadingDigitsPatterns' => [
                '1[016-9]1',
                '1[016-9]11',
                '1[016-9]114',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3,4})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '(?:3[1-3]|[46][1-4]|5[1-5])1',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3,4})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '2',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '[36]0|8',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3,4})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '[1346]|5[1-5]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '[57]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
        [
            'pattern' => '(\\d{5})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '003',
                '0030',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{5})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '5',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
        [
            'pattern' => '(\\d{5})(\\d{3,4})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '0',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{5})(\\d{2})(\\d{3})(\\d{4})',
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
            'pattern' => '(\\d{2})(\\d{3,4})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '(?:3[1-3]|[46][1-4]|5[1-5])1',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
        [
            'pattern' => '(\\d{4})(\\d{4})',
            'format' => '$1-$2',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{3,4})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '2',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '[36]0|8',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{3,4})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '[1346]|5[1-5]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{4})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '[57]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
        [
            'pattern' => '(\\d{2})(\\d{5})(\\d{4})',
            'format' => '$1-$2-$3',
            'leadingDigitsPatterns' => [
                '5',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '0$CC-$1',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
