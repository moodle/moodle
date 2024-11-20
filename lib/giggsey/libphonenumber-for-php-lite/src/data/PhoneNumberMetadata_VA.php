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
    'NationalNumberPattern' => '0\\d{5,10}|3[0-8]\\d{7,10}|55\\d{8}|8\\d{5}(?:\\d{2,4})?|(?:1\\d|39)\\d{7,8}',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 7,
      2 => 8,
      3 => 9,
      4 => 10,
      5 => 11,
      6 => 12,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '06698\\d{1,6}',
    'ExampleNumber' => '0669812345',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 7,
      2 => 8,
      3 => 9,
      4 => 10,
      5 => 11,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '3[1-9]\\d{8}|3[2-9]\\d{7}',
    'ExampleNumber' => '3123456789',
    'PossibleLength' =>
     [
      0 => 9,
      1 => 10,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '80(?:0\\d{3}|3)\\d{3}',
    'ExampleNumber' => '800123456',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 9,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '(?:0878\\d{3}|89(?:2\\d|3[04]|4(?:[0-4]|[5-9]\\d\\d)|5[0-4]))\\d\\d|(?:1(?:44|6[346])|89(?:38|5[5-9]|9))\\d{6}',
    'ExampleNumber' => '899123456',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 8,
      2 => 9,
      3 => 10,
    ],
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '84(?:[08]\\d{3}|[17])\\d{3}',
    'ExampleNumber' => '848123456',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 9,
    ],
  ],
  'personalNumber' =>
   [
    'NationalNumberPattern' => '1(?:78\\d|99)\\d{6}',
    'ExampleNumber' => '1781234567',
    'PossibleLength' =>
     [
      0 => 9,
      1 => 10,
    ],
  ],
  'voip' =>
   [
    'NationalNumberPattern' => '55\\d{8}',
    'ExampleNumber' => '5512345678',
    'PossibleLength' =>
     [
      0 => 10,
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
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'voicemail' =>
   [
    'NationalNumberPattern' => '3[2-8]\\d{9,10}',
    'ExampleNumber' => '33101234501',
    'PossibleLength' =>
     [
      0 => 11,
      1 => 12,
    ],
  ],
  'noInternationalDialling' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'id' => 'VA',
  'countryCode' => 39,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
  ],
  'mainCountryForCode' => false,
  'leadingDigits' => '06698',
  'mobileNumberPortableRegion' => true,
];
