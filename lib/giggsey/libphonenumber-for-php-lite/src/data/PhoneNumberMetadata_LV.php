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
    'NationalNumberPattern' => '(?:[268]\\d|90)\\d{6}',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '6\\d{7}',
    'ExampleNumber' => '63123456',
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '23(?:23[0-57-9]|33[0238])\\d{3}|2(?:[0-24-9]\\d\\d|3(?:0[07]|[14-9]\\d|2[024-9]|3[0-24-9]))\\d{4}',
    'ExampleNumber' => '21234567',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '80\\d{6}',
    'ExampleNumber' => '80123456',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '90\\d{6}',
    'ExampleNumber' => '90123456',
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '81\\d{6}',
    'ExampleNumber' => '81123456',
  ],
  'personalNumber' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'voip' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
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
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'noInternationalDialling' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'id' => 'LV',
  'countryCode' => 371,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '[269]|8[01]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
