<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MF',
    'countryCode' => 590,
    'generalDesc' => [
        'pattern' => '(?:590\\d|7090)\\d{5}|(?:69|80|9\\d)\\d{7}',
        'posLength' => [
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '590(?:0[079]|[14]3|[27][79]|3[03-7]|5[0-268]|87)\\d{4}',
        'example' => '590271234',
    ],
    'mobile' => [
        'pattern' => '(?:69(?:0\\d\\d|1(?:2[2-9]|3[0-5])|4(?:0[89]|1[2-6]|9\\d)|6(?:1[016-9]|5[0-4]|[67]\\d))|7090[0-4])\\d{4}',
        'example' => '690001234',
    ],
    'tollFree' => [
        'pattern' => '80[0-5]\\d{6}',
        'example' => '800012345',
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
        'pattern' => '9(?:(?:39[5-7]|76[018])\\d|475[0-5])\\d{4}',
        'example' => '976012345',
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
    'internationalPrefix' => '00',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '0',
    'numberFormat' => [],
    'mobileNumberPortableRegion' => true,
];
