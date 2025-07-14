<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'SA',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[19]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '11(?:2|6\\d{3})|9(?:11|37|9[7-9])',
        'example' => '112',
        'posLength' => [
            3,
            6,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '112|9(?:11|9[79])',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1(?:00|2|6111)|410|9(?:00|1[89]|9(?:099|22|9[0-3])))|9(?:0[24-79]|11|3[379]|40|66|8[5-9]|9[02-9])',
        'example' => '112',
    ],
    'standardRate' => [
        'pattern' => '141\\d',
        'example' => '1410',
        'posLength' => [
            4,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '1(?:10|41)\\d|90[24679]',
        'example' => '902',
        'posLength' => [
            3,
            4,
        ],
    ],
    'smsServices' => [
        'posLength' => [
            -1,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
