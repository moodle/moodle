<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'AX',
    'countryCode' => 358,
    'generalDesc' => [
        'pattern' => '2\\d{4,9}|35\\d{4,5}|(?:60\\d\\d|800)\\d{4,6}|7\\d{5,11}|(?:[14]\\d|3[0-46-9]|50)\\d{4,8}',
        'posLength' => [
            5,
            6,
            7,
            8,
            9,
            10,
            11,
            12,
        ],
    ],
    'fixedLine' => [
        'pattern' => '18[1-8]\\d{3,6}',
        'example' => '181234567',
        'posLength' => [
            6,
            7,
            8,
            9,
        ],
    ],
    'mobile' => [
        'pattern' => '4946\\d{2,6}|(?:4[0-8]|50)\\d{4,8}',
        'example' => '412345678',
        'posLength' => [
            6,
            7,
            8,
            9,
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '800\\d{4,6}',
        'example' => '800123456',
        'posLength' => [
            7,
            8,
            9,
        ],
    ],
    'premiumRate' => [
        'pattern' => '[67]00\\d{5,6}',
        'example' => '600123456',
        'posLength' => [
            8,
            9,
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
        'pattern' => '20\\d{4,8}|60[12]\\d{5,6}|7(?:099\\d{4,5}|5[03-9]\\d{3,7})|20[2-59]\\d\\d|(?:606|7(?:0[78]|1|3\\d))\\d{7}|(?:10|29|3[09]|70[1-5]\\d)\\d{4,8}',
        'example' => '10112345',
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
    'internationalPrefix' => '00|99(?:[01469]|5(?:[14]1|3[23]|5[59]|77|88|9[09]))',
    'preferredInternationalPrefix' => '00',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [],
    'leadingDigits' => '18',
];
