<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'PE',
    'countryCode' => 51,
    'generalDesc' => [
        'pattern' => '(?:[14-8]|9\\d)\\d{7}',
        'posLength' => [
            8,
            9,
        ],
        'posLengthLocal' => [
            6,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:(?:(?:4[34]|5[14])[0-8]|687)\\d|7(?:173|(?:3[0-8]|55)\\d)|8(?:10[05689]|6(?:0[06-9]|1[6-9]|29)|7(?:0[0569]|[56]0)))\\d{4}|(?:1[0-8]|4[12]|5[236]|6[1-7]|7[246]|8[2-4])\\d{6}',
        'example' => '11234567',
        'posLength' => [
            8,
        ],
        'posLengthLocal' => [
            6,
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '9\\d{8}',
        'example' => '912345678',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{5}',
        'example' => '80012345',
        'posLength' => [
            8,
        ],
    ],
    'premiumRate' => [
        'pattern' => '805\\d{5}',
        'example' => '80512345',
        'posLength' => [
            8,
        ],
    ],
    'sharedCost' => [
        'pattern' => '801\\d{5}',
        'example' => '80112345',
        'posLength' => [
            8,
        ],
    ],
    'personalNumber' => [
        'pattern' => '80[24]\\d{5}',
        'example' => '80212345',
        'posLength' => [
            8,
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
    'internationalPrefix' => '00|19(?:1[124]|77|90)00',
    'preferredInternationalPrefix' => '00',
    'nationalPrefix' => '0',
    'preferredExtnPrefix' => ' Anexo ',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{5})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '80',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d)(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '1',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{2})(\\d{6})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[4-8]',
            ],
            'nationalPrefixFormattingRule' => '(0$1)',
            'domesticCarrierCodeFormattingRule' => '',
        ],
        [
            'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '9',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mobileNumberPortableRegion' => true,
];
