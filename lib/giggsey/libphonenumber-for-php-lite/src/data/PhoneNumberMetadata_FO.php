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
    'NationalNumberPattern' => '[2-9]\\d{5}',
    'PossibleLength' =>
     [
      0 => 6,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:20|[34]\\d|8[19])\\d{4}',
    'ExampleNumber' => '201234',
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:[27][1-9]|5\\d|9[16])\\d{4}',
    'ExampleNumber' => '211234',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '80[257-9]\\d{3}',
    'ExampleNumber' => '802123',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '90(?:[13-5][15-7]|2[125-7]|9\\d)\\d\\d',
    'ExampleNumber' => '901123',
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
    'NationalNumberPattern' => '(?:6[0-36]|88)\\d{4}',
    'ExampleNumber' => '601234',
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
  'id' => 'FO',
  'countryCode' => 298,
  'internationalPrefix' => '00',
  'nationalPrefixForParsing' => '(10(?:01|[12]0|88))',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{6})',
      'format' => '$1',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-9]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '$CC $1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
