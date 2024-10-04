<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'RE',
    'countryCode' => 262,
    'generalDesc' => [
        'pattern' => '709\\d{6}|(?:26|[689]\\d)\\d{7}',
        'posLength' => [
            9,
        ],
    ],
    'fixedLine' => [
        'pattern' => '26(?:2\\d\\d|3(?:0\\d|1[0-6]))\\d{4}',
        'example' => '262161234',
    ],
    'mobile' => [
        'pattern' => '(?:69(?:2\\d\\d|3(?:[06][0-6]|1[013]|2[0-2]|3[0-39]|4\\d|5[0-5]|7[0-37]|8[0-8]|9[0-479]))|7092[0-3])\\d{4}',
        'example' => '692123456',
    ],
    'tollFree' => [
        'pattern' => '80\\d{7}',
        'example' => '801234567',
    ],
    'premiumRate' => [
        'pattern' => '89[1-37-9]\\d{6}',
        'example' => '891123456',
    ],
    'sharedCost' => [
        'pattern' => '8(?:1[019]|2[0156]|84|90)\\d{6}',
        'example' => '810123456',
    ],
    'personalNumber' => [
        'posLength' => [
            -1,
        ],
    ],
    'voip' => [
        'pattern' => '9(?:399[0-3]|479[0-5]|76(?:2[278]|3[0-37]))\\d{4}',
        'example' => '939901234',
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
    'numberFormat' => [
        [
            'pattern' => '(\\d{3})(\\d{2})(\\d{2})(\\d{2})',
            'format' => '$1 $2 $3 $4',
            'leadingDigitsPatterns' => [
                '[26-9]',
            ],
            'nationalPrefixFormattingRule' => '0$1',
            'domesticCarrierCodeFormattingRule' => '',
        ],
    ],
    'mainCountryForCode' => true,
];
