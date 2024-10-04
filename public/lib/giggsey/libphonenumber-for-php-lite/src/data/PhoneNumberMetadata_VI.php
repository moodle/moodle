<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'VI',
    'countryCode' => 1,
    'generalDesc' => [
        'pattern' => '[58]\\d{9}|(?:34|90)0\\d{7}',
        'posLength' => [
            10,
        ],
        'posLengthLocal' => [
            7,
        ],
    ],
    'fixedLine' => [
        'pattern' => '340(?:2(?:0\\d|10|2[06-8]|4[49]|77)|3(?:32|44)|4(?:2[23]|44|7[34]|89)|5(?:1[34]|55)|6(?:2[56]|4[23]|77|9[023])|7(?:1[2-57-9]|2[57]|7\\d)|884|998)\\d{4}',
        'example' => '3406421234',
        'posLengthLocal' => [
            7,
        ],
    ],
    'mobile' => [
        'pattern' => '340(?:2(?:0\\d|10|2[06-8]|4[49]|77)|3(?:32|44)|4(?:2[23]|44|7[34]|89)|5(?:1[34]|55)|6(?:2[56]|4[23]|77|9[023])|7(?:1[2-57-9]|2[57]|7\\d)|884|998)\\d{4}',
        'example' => '3406421234',
        'posLengthLocal' => [
            7,
        ],
    ],
    'tollFree' => [
        'pattern' => '8(?:00|33|44|55|66|77|88)[2-9]\\d{6}',
        'example' => '8002345678',
    ],
    'premiumRate' => [
        'pattern' => '900[2-9]\\d{6}',
        'example' => '9002345678',
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
    'nationalPrefixForParsing' => '([2-9]\\d{6})$|1',
    'nationalPrefixTransformRule' => '340$1',
    'sameMobileAndFixedLinePattern' => true,
    'numberFormat' => [],
    'leadingDigits' => '340',
];
