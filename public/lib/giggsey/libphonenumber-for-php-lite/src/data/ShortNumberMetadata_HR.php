<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'HR',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[19]\\d{1,5}',
        'posLength' => [
            2,
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:12|9[2-4])|9[34]|1(?:16\\d|39)\\d\\d',
        'example' => '93',
        'posLength' => [
            2,
            3,
            5,
            6,
        ],
    ],
    'premiumRate' => [
        'pattern' => '118\\d\\d',
        'example' => '11800',
        'posLength' => [
            5,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:12|9[2-4])|9[34]',
        'example' => '93',
        'posLength' => [
            2,
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1(?:2|6(?:00[06]|1(?:1[17]|23))|8\\d\\d)|3977|9(?:[2-5]|87))|9[34]',
        'example' => '93',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '139\\d\\d',
        'example' => '13900',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '139\\d\\d',
        'example' => '13900',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
