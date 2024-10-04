<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'JE',
    'countryCode' => 44,
    'generalDesc' => [
        'pattern' => '1534\\d{6}|(?:[3578]\\d|90)\\d{8}',
        'posLength' => [
            10,
        ],
        'posLengthLocal' => [
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '1534[0-24-8]\\d{5}',
        'example' => '1534456789',
        'posLengthLocal' => [
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '7(?:(?:(?:50|82)9|937)\\d|7(?:00[378]|97\\d))\\d{5}',
        'example' => '7797712345',
    ],
    'tollFree' => [
        'pattern' => '80(?:07(?:35|81)|8901)\\d{4}',
        'example' => '8007354567',
    ],
    'premiumRate' => [
        'pattern' => '(?:8(?:4(?:4(?:4(?:05|42|69)|703)|5(?:041|800))|7(?:0002|1206))|90(?:066[59]|1810|71(?:07|55)))\\d{4}',
        'example' => '9018105678',
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'pattern' => '701511\\d{4}',
        'example' => '7015115678',
    ],
    'voip' => [
        'pattern' => '56\\d{8}',
        'example' => '5612345678',
    ],
    'pager' => [
        'pattern' => '76(?:464|652)\\d{5}|76(?:0[0-28]|2[356]|34|4[01347]|5[49]|6[0-369]|77|8[14]|9[139])\\d{6}',
        'example' => '7640123456',
    ],
    'uan' => [
        'pattern' => '(?:3(?:0(?:07(?:35|81)|8901)|3\\d{4}|4(?:4(?:4(?:05|42|69)|703)|5(?:041|800))|7(?:0002|1206))|55\\d{4})\\d{4}',
        'example' => '5512345678',
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
    'nationalPrefixForParsing' => '([0-24-8]\\d{5})$|0',
    'nationalPrefixTransformRule' => '1534$1',
    'numberFormat' => [],
];
