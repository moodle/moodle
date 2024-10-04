<?php

/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return [
    'id' => 'PT',
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
        'pattern' => '11[257]|1(?:16\\d\\d|5[1589]|8[279])\\d',
        'example' => '112',
    ],
    'premiumRate' => [
        'posLength' => [
            -1,
        ],
    ],
    'emergency' => [
        'pattern' => '11[25]',
        'example' => '112',
        'posLength' => [
            3,
        ],
    ],
    'shortCode' => [
        'pattern' => '1(?:0(?:45|5[01])|1(?:[2578]|600[06])|4(?:1[45]|4)|583|6(?:1[0236]|3[02]|9[169]))|1(?:1611|59)1|1[068]78|1[08]9[16]|1(?:0[1-38]|40|5[15]|6[258]|82)0',
        'example' => '112',
    ],
    'standardRate' => [
        'posLength' => [
            -1,
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
