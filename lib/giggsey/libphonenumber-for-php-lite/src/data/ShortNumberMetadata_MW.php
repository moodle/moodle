<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'MW',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[189]\\d\\d(?:\\d{2})?',
        'posLength' => [
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '199|99[7-9]',
        'example' => '199',
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
        'pattern' => '199|99[7-9]',
        'example' => '199',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '199|80400|99[7-9]',
        'example' => '199',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '804\\d\\d',
        'example' => '80400',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '804\\d\\d',
        'example' => '80400',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
