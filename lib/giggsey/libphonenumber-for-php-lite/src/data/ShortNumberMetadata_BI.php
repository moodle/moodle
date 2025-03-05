<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BI',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[16-9]\\d{2,3}',
        'posLength' => [
            3,
            4,
        ],
    ],
    'tollFree' => [
        'pattern' => '11[237]|611',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '11[237]',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1\\d|5[2-9]|6[0-256])|611|7(?:10|77|979)|8[28]8|900',
        'example' => '110',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '611|7(?:10|77)|888|900',
        'example' => '611',
        'posLength' => [
            3,
        ],
    ],
    'smsServices' => [
        'pattern' => '(?:71|90)0',
        'example' => '710',
        'posLength' => [
            3,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
