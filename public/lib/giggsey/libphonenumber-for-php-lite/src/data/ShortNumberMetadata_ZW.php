<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'ZW',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[139]\\d\\d(?:\\d{2})?',
        'posLength' => [
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '112|9(?:5[023]|61|9[3-59])',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'premiumRate' => [
        'pattern' => '3[013-57-9]\\d{3}',
        'example' => '30000',
        'posLength' => [
            5,
        ],
    ],
    'emergency' => [
        'pattern' => '112|99[3-59]',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '11[2469]|3[013-57-9]\\d{3}|9(?:5[023]|6[0-25]|9[3-59])',
        'example' => '112',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '114|9(?:5[023]|6[0-25])',
        'example' => '114',
        'posLength' => [
            3,
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
