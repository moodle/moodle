<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'GR',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '1\\d\\d(?:\\d{2,3})?',
        'posLength' => [
            3,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:0[089]|1(?:2|6\\d{3})|66|99)',
        'example' => '100',
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
        'pattern' => '1(?:00|12|66|99)',
        'example' => '100',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0[089]|1(?:2|320|6(?:000|1(?:1[17]|23)))|(?:389|9)9|66)',
        'example' => '100',
    ],
    'standardRate' => [
        'pattern' => '113\\d\\d',
        'example' => '11300',
        'posLength' => [
            5,
        ],
    ],
    'carrierSpecific' => [
        'posLength' => [
            -1,
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
