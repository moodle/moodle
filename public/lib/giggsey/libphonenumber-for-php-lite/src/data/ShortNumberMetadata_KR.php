<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'KR',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '1\\d{2,4}',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1[27-9]|28|330|82)',
        'example' => '112',
        'posLength' => [
            3,
            4,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '11[29]',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:[016-9]114|3(?:0[01]|2|3[0-35-9]|45?|5[057]|6[569]|7[79]|8[2589]|9[0189]))|1(?:0[015]|1\\d|2[01357-9]|41|8[28])',
        'example' => '100',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '1(?:0[01]|1[4-6]|41)|1(?:[06-9]1\\d|111)\\d',
        'example' => '100',
        'posLength' => [
            3,
            5,
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
