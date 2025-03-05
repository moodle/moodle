<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'IE',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[159]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '11(?:2|6\\d{3})|999',
        'example' => '112',
        'posLength' => [
            3,
            6,
        ],
    ],
    'premiumRate' => [
        'pattern' => '5[37]\\d{3}',
        'example' => '53000',
        'posLength' => [
            5,
        ],
    ],
    'emergency' => [
        'pattern' => '112|999',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '11(?:2|6(?:00[06]|1(?:1[17]|23)))|999|(?:1(?:18|9)|5[0137]\\d)\\d\\d',
        'example' => '112',
    ],
    'standardRate' => [
        'pattern' => '51\\d{3}',
        'example' => '51000',
        'posLength' => [
            5,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '51210',
        'example' => '51210',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '51210|(?:118|5[037]\\d)\\d\\d',
        'example' => '11800',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
