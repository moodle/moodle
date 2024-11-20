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
    'NationalNumberPattern' => '(?:[378]\\d|93)\\d{7}',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '3(?:0(?:1[0-2]|80)|282|3(?:8[1-9]|9[3-9])|611)\\d{5}',
    'ExampleNumber' => '301012345',
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '7(?:(?:[06-8]\\d|21|90)\\d|5(?:01|[19]0|25|[38]3|[4-7]\\d))\\d{5}',
    'ExampleNumber' => '701234567',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800\\d{6}',
    'ExampleNumber' => '800123456',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '88[4689]\\d{6}',
    'ExampleNumber' => '884123456',
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '81[02468]\\d{6}',
    'ExampleNumber' => '810123456',
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
    'NationalNumberPattern' => '(?:3(?:392|9[01]\\d)\\d|93(?:3[13]0|929))\\d{4}',
    'ExampleNumber' => '933301234',
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
  'id' => 'SN',
  'countryCode' => 221,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{2})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '8',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '[379]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
