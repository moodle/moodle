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
    'NationalNumberPattern' => '(?:[1-6]|[7-9]\\d\\d)\\d{4}',
    'PossibleLength' =>
     [
      0 => 5,
      1 => 7,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:1[4-79]|[23]\\d|4[0-2]|5[03]|6[0-37])\\d{3}',
    'ExampleNumber' => '40123',
    'PossibleLength' =>
     [
      0 => 5,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '48\\d{3}|(?:(?:7[1-9]|8[4-9])\\d|9(?:1[2-9]|2[013-9]|3[0-2]|[46]\\d|5[0-46-9]|7[0-689]|8[0-79]|9[0-8]))\\d{4}',
    'ExampleNumber' => '7421234',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '1[38]\\d{3}',
    'ExampleNumber' => '18123',
    'PossibleLength' =>
     [
      0 => 5,
    ],
  ],
  'premiumRate' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'sharedCost' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
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
    'NationalNumberPattern' => '5[12]\\d{3}',
    'ExampleNumber' => '51123',
    'PossibleLength' =>
     [
      0 => 5,
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
  'id' => 'SB',
  'countryCode' => 677,
  'internationalPrefix' => '0[01]',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{2})(\\d{5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '7|8[4-9]|9(?:[1-8]|9[0-8])',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
