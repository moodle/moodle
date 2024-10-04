<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'IS',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '1\\d\\d(?:\\d(?:\\d{2})?)?',
        'posLength' => [
            3,
            4,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:12|71\\d)',
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
        'pattern' => '112',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:1(?:[28]|61(?:16|23))|4(?:00|1[145]|4[0146])|55|7(?:00|17|7[07-9])|8(?:[02]0|1[16-9]|88)|900)',
        'example' => '112',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '14(?:0\\d|41)',
        'example' => '1400',
        'posLength' => [
            4,
        ],
    ],
    'smsServices' => [
        'pattern' => '1(?:415|90\\d)',
        'example' => '1415',
        'posLength' => [
            4,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
