<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'EG',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[13]\\d\\d(?:\\d{2})?',
        'posLength' => [
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:2[23]|80)',
        'example' => '122',
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
        'pattern' => '1(?:2[23]|80)',
        'example' => '122',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:2[23]|[679]\\d{3}|80)|34400',
        'example' => '122',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '344\\d\\d',
        'example' => '34400',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '344\\d\\d',
        'example' => '34400',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
