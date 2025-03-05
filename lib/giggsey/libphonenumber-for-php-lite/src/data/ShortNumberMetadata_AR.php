<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'AR',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[01389]\\d{1,4}',
        'posLength' => [
            2,
            3,
            4,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '000|1(?:0[0-35-7]|1[0245]|2[015]|3[47]|4[478]|9)|911',
        'example' => '19',
        'posLength' => [
            2,
            3,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '10[017]|911',
        'example' => '100',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '000|1(?:0[0-35-7]|1[02-5]|2[015]|3[47]|4[478]|9)|3372|89338|911',
        'example' => '19',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '893\\d\\d',
        'example' => '89300',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '(?:337|893\\d)\\d',
        'example' => '3370',
        'posLength' => [
            4,
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
