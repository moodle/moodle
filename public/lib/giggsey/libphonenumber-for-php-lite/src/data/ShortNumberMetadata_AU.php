<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'AU',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[0-27]\\d{2,7}',
        'posLength' => [
            3,
            4,
            5,
            6,
            7,
            8,
        ],
    ],
    'tollFree' => [
        'pattern' => '000|1(?:06|12|258885|55\\d)|733',
        'example' => '000',
        'posLength' => [
            3,
            4,
            7,
        ],
    ],
    'premiumRate' => [
        'pattern' => '1(?:2(?:34|456)|9\\d{4,6})',
        'example' => '1234',
        'posLength' => [
            4,
            5,
            6,
            7,
            8,
        ],
    ],
    'emergency' => [
        'pattern' => '000|1(?:06|12)',
        'example' => '000',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '000|1(?:06|1(?:00|2|9[46])|2(?:014[1-3]|[23]\\d|(?:4|5\\d)\\d{2,3}|68[689]|72(?:20|3\\d\\d)|8(?:[013-9]\\d|2))|555|9\\d{4,6})|225|7(?:33|67)',
        'example' => '000',
    ],
    'standardRate' => [
        'pattern' => '1(?:1[09]\\d|24733)|225|767',
        'example' => '225',
        'posLength' => [
            3,
            4,
            6,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '1(?:258885|55\\d)',
        'example' => '1550',
        'posLength' => [
            4,
            7,
        ],
    ],
    'smsServices' => [
        'pattern' => '19\\d{4,6}',
        'example' => '190000',
        'posLength' => [
            6,
            7,
            8,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
