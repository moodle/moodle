<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'IM',
    'countryCode' => 44,
    'generalDesc' => [
        'pattern' => '1624\\d{6}|(?:[3578]\\d|90)\\d{8}',
        'posLength' => [
            10,
        ],
        'posLengthLocal' => [
            6,
        ],
    ],
    'fixedLine' => [
        'pattern' => '1624(?:230|[5-8]\\d\\d)\\d{3}',
        'example' => '1624756789',
        'posLengthLocal' => [
            6,
        ],
    ],
    'mobile' => [
        'pattern' => '76245[06]\\d{4}|7(?:4576|[59]24\\d|624[0-4689])\\d{5}',
        'example' => '7924123456',
    ],
    'tollFree' => [
        'pattern' => '808162\\d{4}',
        'example' => '8081624567',
    ],
    'premiumRate' => [
        'pattern' => '8(?:440[49]06|72299\\d)\\d{3}|(?:8(?:45|70)|90[0167])624\\d{4}',
        'example' => '9016247890',
    ],
    'sharedCost' => [
        'posLength' => [
            -1,
        ],
    ],
    'personalNumber' => [
        'pattern' => '70\\d{8}',
        'example' => '7012345678',
    ],
    'voip' => [
        'pattern' => '56\\d{8}',
        'example' => '5612345678',
    ],
    'pager' => [
        'posLength' => [
            -1,
        ],
    ],
    'uan' => [
        'pattern' => '3440[49]06\\d{3}|(?:3(?:08162|3\\d{4}|45624|7(?:0624|2299))|55\\d{4})\\d{4}',
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
    'nationalPrefixForParsing' => '([25-8]\\d{5})$|0',
    'nationalPrefixTransformRule' => '1624$1',
    'numberFormat' => [],
    'leadingDigits' => '74576|(?:16|7[56])24',
];
