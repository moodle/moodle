<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'BD',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1579]\\d{2,4}',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'tollFree' => [
        'pattern' => '10[0-26]|[19]99',
        'example' => '100',
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
        'pattern' => '10[0-2]|[19]99',
        'example' => '100',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0(?:[0-369]|5[1-4]|7[0-4]|8[0-29])|1[16-9]|2(?:[134]|2[0-5])|3(?:1\\d?|6[3-6])|5[2-9])|5012|786|9594|[19]99|1(?:0(?:50|6\\d)|33|4(?:0|1\\d))\\d',
        'example' => '100',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '1(?:11|2[13])|(?:501|959)\\d|786',
        'example' => '111',
        'posLength' => [
            3,
            4,
        ],
    ],
    'smsServices' => [
        'pattern' => '959\\d',
        'example' => '9590',
        'posLength' => [
            4,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
