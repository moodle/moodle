<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BO',
    'countryCode' => 591,
    'generalDesc' => [
        'pattern' => '8001\\d{5}|(?:[2-467]\\d|50)\\d{6}',
        'posLength' => [
            8,
            9,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:2(?:2\\d\\d|5(?:11|[258]\\d|9[67])|6(?:12|2\\d|9[34])|8(?:2[34]|39|62))|3(?:3\\d\\d|4(?:6\\d|8[24])|8(?:25|42|5[257]|86|9[25])|9(?:[27]\\d|3[2-4]|4[248]|5[24]|6[2-6]))|4(?:4\\d\\d|6(?:11|[24689]\\d|72)))\\d{4}',
        'example' => '22123456',
        'posLength' => [
            8,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '[67]\\d{7}',
        'example' => '71234567',
        'posLength' => [
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '8001[07]\\d{4}',
        'example' => '800171234',
        'posLength' => [
            9,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
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
        'pattern' => '50\\d{6}',
        'example' => '50123456',
        'posLength' => [
            8,
        ],
        'posLengthLocal' => [
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
        'pattern' => '8001[07]\\d{4}',
        'posLength' => [
            9,
        ],
    ],
    'internationalPrefix' => '00(?:1\\d)?',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0(1\\d)?',
    'numberFormat' => [
        [
            'pattern' => '(\\d)(\\d{7})',
            'format' => '$1 $2',
            'leadingDigitsPatterns' => [
                '[235]|4[46]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '0$CC $1',
        ],
        [
            'pattern' => '(\\d{8})',
            'format' => '$1',
            'leadingDigitsPatterns' => [
                '[67]',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '0$CC $1',
        ],
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{4})',
            'format' => '$1 $2 $3',
            'leadingDigitsPatterns' => [
                '8',
            ],
            'nationalPrefixFormattingRule' => '',
            'domesticCarrierCodeFormattingRule' => '0$CC $1',
        ],
    ],
];
