<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CX',
    'countryCode' => 61,
    'generalDesc' => [
        'pattern' => '1(?:[0-79]\\d{8}(?:\\d{2})?|8[0-24-9]\\d{7})|[148]\\d{8}|1\\d{5,7}',
        'posLength' => [
            6,
            7,
            8,
            9,
            10,
            12,
        ],
    ],
    'fixedLine' => [
        'pattern' => '8(?:51(?:0(?:01|30|59|88)|1(?:17|46|75)|2(?:22|35))|91(?:00[6-9]|1(?:[28]1|49|78)|2(?:09|63)|3(?:12|26|75)|4(?:56|97)|64\\d|7(?:0[01]|1[0-2])|958))\\d{3}',
        'example' => '891641234',
        'posLength' => [
            9,
        ],
        'posLengthLocal' => [
            8,
        ],
    ],
    'mobile' => [
        'pattern' => '4(?:79[01]|83[0-389]|94[0-4])\\d{5}|4(?:[0-36]\\d|4[047-9]|5[0-25-9]|7[02-8]|8[0-24-9]|9[0-37-9])\\d{6}',
        'example' => '412345678',
        'posLength' => [
            9,
        ],
    ],
    'tollFree' => [
        'pattern' => '180(?:0\\d{3}|2)\\d{3}',
        'example' => '1800123456',
        'posLength' => [
            7,
            10,
        ],
    ],
    'premiumRate' => [
        'pattern' => '190[0-26]\\d{6}',
        'example' => '1900123456',
        'posLength' => [
            10,
        ],
    ],
    'sharedCost' => [
        'pattern' => '13(?:00\\d{6}(?:\\d{2})?|45[0-4]\\d{3})|13\\d{4}',
        'example' => '1300123456',
        'posLength' => [
            6,
            8,
            10,
            12,
        ],
    ],
    'personalNumber' => [
        'posLength' => [
            -1,
        ],
    ],
    'voip' => [
        'pattern' => '14(?:5(?:1[0458]|[23][458])|71\\d)\\d{4}',
        'example' => '147101234',
        'posLength' => [
            9,
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
    'internationalPrefix' => '001[14-689]|14(?:1[14]|34|4[17]|[56]6|7[47]|88)0011',
    'preferredInternationalPrefix' => '0011',
    'nationalPrefix' => '0',
    'nationalPrefixForParsing' => '([59]\\d{7})$|0',
    'nationalPrefixTransformRule' => '8$1',
    'numberFormat' => [],
];
