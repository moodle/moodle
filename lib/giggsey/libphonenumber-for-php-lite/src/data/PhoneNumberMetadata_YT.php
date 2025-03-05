<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'YT',
    'countryCode' => 262,
    'generalDesc' => [
        'pattern' => '7093\\d{5}|(?:80|9\\d)\\d{7}|(?:26|63)9\\d{6}',
        'posLength' => [
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '269(?:0[0-467]|15|5[0-4]|6\\d|[78]0)\\d{4}',
        'example' => '269601234',
    ],
    'mobile' => [
        'pattern' => '(?:639(?:0[0-79]|1[019]|[267]\\d|3[09]|40|5[05-9]|9[04-79])|7093[5-7])\\d{4}',
        'example' => '639012345',
    ],
    'tollFree' => [
        'pattern' => '80\\d{7}',
        'example' => '801234567',
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
        'pattern' => '9(?:(?:39|47)8[01]|769\\d)\\d{4}',
        'example' => '939801234',
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
];
