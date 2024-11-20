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
    'NationalNumberPattern' => '(?:800|9[0-57-9]\\d)\\d{7}|[34679]\\d{6}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 10,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:3(?:0[0-3]|3[0-59])|6(?:[58][024689]|6[024-68]|7[02468]))\\d{4}',
    'ExampleNumber' => '6701234',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:46[46]|[79]\\d\\d)\\d{4}',
    'ExampleNumber' => '7712345',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800\\d{7}',
    'ExampleNumber' => '8001234567',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '900\\d{7}',
    'ExampleNumber' => '9001234567',
    'PossibleLength' =>
     [
      0 => 10,
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
    'NationalNumberPattern' => '4(?:0[01]|50)\\d{4}',
    'ExampleNumber' => '4001234',
    'PossibleLength' =>
     [
      0 => 7,
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
  'id' => 'MV',
  'countryCode' => 960,
  'internationalPrefix' => '0(?:0|19)',
  'preferredInternationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{4})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '[34679]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '[89]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
