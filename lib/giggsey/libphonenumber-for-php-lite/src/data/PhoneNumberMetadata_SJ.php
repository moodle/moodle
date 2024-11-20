<?php
/**
 * libphonenumber-for-php-lite data file
 * This file has been @generated from libphonenumber data
 * Do not modify!
 * @internal
 */

return  [
  'generalDesc' =>
   [
    'NationalNumberPattern' => '0\\d{4}|(?:[489]\\d|79)\\d{6}',
    'PossibleLength' =>
     [
      0 => 5,
      1 => 8,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '79\\d{6}',
    'ExampleNumber' => '79123456',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:4[015-8]|9\\d)\\d{6}',
    'ExampleNumber' => '41234567',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '80[01]\\d{5}',
    'ExampleNumber' => '80012345',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '82[09]\\d{5}',
    'ExampleNumber' => '82012345',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '810(?:0[0-6]|[2-8]\\d)\\d{3}',
    'ExampleNumber' => '81021234',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'personalNumber' =>
   [
    'NationalNumberPattern' => '880\\d{5}',
    'ExampleNumber' => '88012345',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'voip' =>
   [
    'NationalNumberPattern' => '85[0-5]\\d{5}',
    'ExampleNumber' => '85012345',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'pager' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'uan' =>
   [
    'NationalNumberPattern' => '(?:0[2-9]|81(?:0(?:0[7-9]|1\\d)|5\\d\\d))\\d{3}',
    'ExampleNumber' => '02000',
  ],
  'voicemail' =>
   [
    'NationalNumberPattern' => '81[23]\\d{5}',
    'ExampleNumber' => '81212345',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'noInternationalDialling' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'id' => 'SJ',
  'countryCode' => 47,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
  ],
  'mainCountryForCode' => false,
  'leadingDigits' => '79',
  'mobileNumberPortableRegion' => false,
];
