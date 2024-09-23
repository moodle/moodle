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
    'NationalNumberPattern' => '800\\d{4}|(?:2|800)\\d{6}|(?:0080|[3-7])\\d{7}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
      2 => 9,
      3 => 11,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '4(?:1111|2022)\\d{3}|4(?:[04]\\d\\d|14[0-6]|999)\\d{4}',
    'ExampleNumber' => '44123456',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '[35-7]\\d{7}',
    'ExampleNumber' => '33123456',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800\\d{4}|(?:0080[01]|800)\\d{6}',
    'ExampleNumber' => '8001234',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 9,
      2 => 11,
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
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'pager' =>
   [
    'NationalNumberPattern' => '2[16]\\d{5}',
    'ExampleNumber' => '2123456',
    'PossibleLength' =>
     [
      0 => 7,
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
  'id' => 'QA',
  'countryCode' => 974,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '2[16]|8',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{4})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[3-7]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
