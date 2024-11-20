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
    'NationalNumberPattern' => '(?:1|6\\d)\\d{7}|[135-9]\\d{5}',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 8,
      2 => 9,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '[78]\\d{5}',
    'ExampleNumber' => '712345',
    'PossibleLength' =>
     [
      0 => 6,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '690\\d{6}|[356]\\d{5}',
    'ExampleNumber' => '312345',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 9,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '180[02]\\d{4}',
    'ExampleNumber' => '18001234',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '[19]\\d{5}',
    'ExampleNumber' => '912345',
    'PossibleLength' =>
     [
      0 => 6,
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
    'NationalNumberPattern' => '1800\\d{4}',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'id' => 'AD',
  'countryCode' => 376,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{3})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[135-9]',
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
        0 => '1',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '6',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
