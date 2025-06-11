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
    'NationalNumberPattern' => '(?:1505|[279]\\d{3}|500)\\d{4}|800\\d{5,6}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
      2 => 9,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '2[1-6]\\d{6}',
    'ExampleNumber' => '23123456',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:1505|90[1-9]\\d)\\d{4}|(?:7[126-9]|9[1-9])\\d{6}',
    'ExampleNumber' => '92123456',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '8007\\d{4,5}|(?:500|800[05])\\d{4}',
    'ExampleNumber' => '80071234',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '900\\d{5}',
    'ExampleNumber' => '90012345',
    'PossibleLength' =>
     [
      0 => 8,
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
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'id' => 'OM',
  'countryCode' => 968,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{4,6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[58]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '2',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{4})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[179]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
