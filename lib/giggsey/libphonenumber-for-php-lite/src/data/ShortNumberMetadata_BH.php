<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BH',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[0189]\\d\\d(?:\\d{2})?',
        'posLength' => [
            3,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '(?:0[167]|81)\\d{3}|[19]99',
        'example' => '199',
    ],
    'premiumRate' => [
        'pattern' => '9[148]\\d{3}',
        'example' => '91000',
        'posLength' => [
            5,
        ],
    ],
    'emergency' => [
        'pattern' => '[19]99',
        'example' => '199',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:[02]\\d|12|4[01]|51|8[18]|9[169])|99[02489]|(?:0[167]|8[158]|9[148])\\d{3}',
        'example' => '100',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '0[67]\\d{3}|88000|98555',
        'example' => '06000',
        'posLength' => [
            5,
        ],
    ],
    'smsServices' => [
        'pattern' => '88000|98555',
        'example' => '88000',
        'posLength' => [
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
