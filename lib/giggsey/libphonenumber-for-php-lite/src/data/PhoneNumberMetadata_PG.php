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
    'NationalNumberPattern' => '(?:180|[78]\\d{3})\\d{4}|(?:[2-589]\\d|64)\\d{5}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:(?:3[0-2]|4[257]|5[34]|9[78])\\d|64[1-9]|85[02-46-9])\\d{4}',
    'ExampleNumber' => '3123456',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:7\\d|8[1-38])\\d{6}',
    'ExampleNumber' => '70123456',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '180\\d{4}',
    'ExampleNumber' => '1801234',
    'PossibleLength' =>
     [
      0 => 7,
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
    'NationalNumberPattern' => '2(?:0[0-57]|7[568])\\d{4}',
    'ExampleNumber' => '2751234',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'pager' =>
   [
    'NationalNumberPattern' => '27[01]\\d{4}',
    'ExampleNumber' => '2700123',
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
  'id' => 'PG',
  'countryCode' => 675,
  'internationalPrefix' => '00|140[1-3]',
  'preferredInternationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '18|[2-69]|85',
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
        0 => '[78]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
