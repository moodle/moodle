<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'KZ',
    'countryCode' => 7,
    'generalDesc' => [
        'pattern' => '(?:33622|8\\d{8})\\d{5}|[78]\\d{9}',
        'posLength' => [
            10,
            14,
        ],
        'posLengthLocal' => [
            5,
            6,
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '(?:33622|7(?:1(?:0(?:[23]\\d|4[0-3]|59|63)|1(?:[23]\\d|4[0-79]|59)|2(?:[23]\\d|59)|3(?:2\\d|3[0-79]|4[0-35-9]|59)|4(?:[24]\\d|3[013-9]|5[1-9]|97)|5(?:2\\d|3[1-9]|4[0-7]|59)|6(?:[2-4]\\d|5[19]|61)|72\\d|8(?:[27]\\d|3[1-46-9]|4[0-5]|59))|2(?:1(?:[23]\\d|4[46-9]|5[3469])|2(?:2\\d|3[0679]|46|5[12679])|3(?:[2-4]\\d|5[139])|4(?:2\\d|3[1-35-9]|59)|5(?:[23]\\d|4[0-8]|59|61)|6(?:2\\d|3[1-9]|4[0-4]|59)|7(?:[2379]\\d|40|5[279])|8(?:[23]\\d|4[0-3]|59)|9(?:2\\d|3[124578]|59))))\\d{5}',
        'example' => '7123456789',
        'posLength' => [
            10,
        ],
        'posLengthLocal' => [
            5,
            6,
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '7(?:0[0-25-8]|47|6[0-4]|7[15-8]|85)\\d{7}',
        'example' => '7710009998',
        'posLength' => [
            10,
        ],
    ],
    'tollFree' => [
        'pattern' => '8(?:00|108\\d{3})\\d{7}',
        'example' => '8001234567',
    ],
    'premiumRate' => [
        'pattern' => '809\\d{7}',
        'example' => '8091234567',
        'posLength' => [
            10,
        ],
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'pattern' => '808\\d{7}',
        'example' => '8081234567',
        'posLength' => [
            10,
        ],
    ],
    'voip' => [
        'pattern' => '751\\d{7}',
        'example' => '7511234567',
        'posLength' => [
            10,
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
        'pattern' => '751\\d{7}',
        'posLength' => [
            10,
        ],
    ],
    'internationalPrefix' => '810',
    'preferredInternationalPrefix' => '8~10',
    'nationalPrefix' => '8',
    'nationalPrefixForParsing' => '8',
    'numberFormat' => [],
    'leadingDigits' => '33|7',
    'mobileNumberPortableRegion' => true,
];
