<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'DM',
    'countryCode' => 1,
    'generalDesc' => [
        'pattern' => '(?:[58]\\d\\d|767|900)\\d{7}',
        'posLength' => [
            10,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '767(?:2(?:55|66)|4(?:2[01]|4[0-25-9])|50[0-4])\\d{4}',
        'example' => '7674201234',
        'posLengthLocal' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '767(?:2(?:[2-4689]5|7[5-7])|31[5-7]|61[1-8]|70[1-6])\\d{4}',
        'example' => '7672251234',
        'posLengthLocal' => [
            7,
        ],
    ],
    'tollFree' => [
        'pattern' => '8(?:00|33|44|55|66|77|88)[2-9]\\d{6}',
        'example' => '8002123456',
    ],
    'premiumRate' => [
        'pattern' => '900[2-9]\\d{6}',
        'example' => '9002123456',
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'pattern' => '52(?:3(?:[2-46-9][02-9]\\d|5(?:[02-46-9]\\d|5[0-46-9]))|4(?:[2-478][02-9]\\d|5(?:[034]\\d|2[024-9]|5[0-46-9])|6(?:0[1-9]|[2-9]\\d)|9(?:[05-9]\\d|2[0-5]|49)))\\d{4}|52[34][2-9]1[02-9]\\d{4}|5(?:00|2[125-9]|33|44|66|77|88)[2-9]\\d{6}',
        'example' => '5002345678',
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
    'internationalPrefix' => '011',
    'nationalPrefix' => '1',
    'nationalPrefixForParsing' => '([2-7]\\d{6})$|1',
    'nationalPrefixTransformRule' => '767$1',
    'numberFormat' => [],
    'leadingDigits' => '767',
    'mobileNumberPortableRegion' => true,
];
