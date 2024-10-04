<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'CH',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1-9]\\d{2,5}',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1(?:[278]|6\\d{3})|4[47])|5200',
        'example' => '112',
        'posLength' => [
            3,
            4,
            6,
        ],
    ],
    'premiumRate' => [
        'pattern' => '1(?:14|8[0-2589])\\d|543|83111',
        'example' => '543',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:1[278]|44)',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0[78]\\d\\d|1(?:[278]|45|6(?:000|111))|4(?:[03-57]|1[0145])|6(?:00|[1-46])|8(?:02|1[189]|[25]0|7|8[08]|99))|[2-9]\\d{2,4}',
        'example' => '112',
    ],
    'standardRate' => [
        'pattern' => '1(?:4[035]|6[1-46])|1(?:41|60)\\d',
        'example' => '140',
        'posLength' => [
            3,
            4,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '5(?:200|35)',
        'example' => '535',
        'posLength' => [
            3,
            4,
        ],
    ],
    'smsServices' => [
        'pattern' => '[2-9]\\d{2,4}',
        'example' => '200',
        'posLength' => [
            3,
            4,
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
