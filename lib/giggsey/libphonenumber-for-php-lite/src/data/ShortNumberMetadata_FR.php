<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'FR',
    'countryCode' => 0,
    'generalDesc' => [
        'pattern' => '[1-8]\\d{1,5}',
        'posLength' => [
            2,
            3,
            4,
            5,
            6,
        ],
    ],
    'tollFree' => [
        'pattern' => '1(?:1[02459]|[578]|9[167])|224|(?:3370|74)0|(?:116\\d|3[01])\\d\\d',
        'example' => '15',
    ],
    'premiumRate' => [
        'pattern' => '(?:1(?:0|18\\d)|366|[4-8]\\d\\d)\\d\\d|3[2-9]\\d\\d',
        'example' => '1000',
        'posLength' => [
            4,
            5,
            6,
        ],
    ],
    'emergency' => [
        'pattern' => '1(?:12|[578])',
        'example' => '15',
        'posLength' => [
            2,
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0\\d\\d|1(?:[02459]|6(?:000|111)|8\\d{3})|[578]|9[167])|2(?:0(?:00|2)0|24)|[3-8]\\d{4}|3\\d{3}|6(?:1[14]|34)|7(?:0[06]|22|40)',
        'example' => '15',
    ],
    'standardRate' => [
        'pattern' => '202\\d|6(?:1[14]|34)|70[06]',
        'example' => '611',
        'posLength' => [
            3,
            4,
        ],
    ],
    'carrierSpecific' => [
        'pattern' => '118777|224|6(?:1[14]|34)|7(?:0[06]|22|40)|20(?:0\\d|2)\\d',
        'example' => '224',
        'posLength' => [
            3,
            4,
            5,
            6,
        ],
    ],
    'smsServices' => [
        'pattern' => '114|[3-8]\\d{4}',
        'example' => '114',
        'posLength' => [
            3,
            5,
        ],
    ],
    'internationalPrefix' => '',
    'numberFormat' => [],
];
