<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GG',
    'countryCode' => 44,
    'generalDesc' => [
        'pattern' => '(?:1481|[357-9]\\d{3})\\d{6}|8\\d{6}(?:\\d{2})?',
        'posLength' => [
            7,
            9,
            10,
        ],
        'posLengthLocal' => [
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '1481[25-9]\\d{5}',
        'example' => '1481256789',
        'posLength' => [
            10,
        ],
        'posLengthLocal' => [
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '7(?:(?:781|839)\\d|911[17])\\d{5}',
        'example' => '7781123456',
        'posLength' => [
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '80[08]\\d{7}|800\\d{6}|8001111',
        'example' => '8001234567',
    ],
    'premiumRate' => [
        'pattern' => '(?:8(?:4[2-5]|7[0-3])|9(?:[01]\\d|8[0-3]))\\d{7}|845464\\d',
        'example' => '9012345678',
        'posLength' => [
            7,
            10,
        ],
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
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
        'pattern' => '56\\d{8}',
        'example' => '5612345678',
        'posLength' => [
            10,
        ],
    ],
    'pager' => [
        'pattern' => '76(?:464|652)\\d{5}|76(?:0[0-28]|2[356]|34|4[01347]|5[49]|6[0-369]|77|8[14]|9[139])\\d{6}',
        'example' => '7640123456',
        'posLength' => [
            10,
        ],
    ],
    'uan' => [
        'pattern' => '(?:3[0347]|55)\\d{8}',
        'example' => '5512345678',
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
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '([25-9]\\d{5})$|0',
    'nationalPrefixTransformRule' => '1481$1',
    'numberFormat' => [],
];
