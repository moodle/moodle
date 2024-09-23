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
    'NationalNumberPattern' => '0800\\d{4}|(?:[237]\\d|900)\\d{6}',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 9,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '[23][2-5]\\d{6}',
    'ExampleNumber' => '22171234',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '7[6-9]\\d{6}',
    'ExampleNumber' => '76123456',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '0800\\d{4}',
    'ExampleNumber' => '08001234',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '900\\d{6}',
    'ExampleNumber' => '900012345',
    'PossibleLength' =>
     [
      0 => 9,
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
    'NationalNumberPattern' => '70\\d{6}',
    'ExampleNumber' => '70012345',
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
    'NationalNumberPattern' => '0800\\d{4}',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'id' => 'SZ',
  'countryCode' => 268,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{4})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[0237]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{5})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '9',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
