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
    'NationalNumberPattern' => '800\\d{6}|(?:[29]0|[347]\\d)\\d{7}',
    'PossibleLength' =>
     [
      0 => 9,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
      1 => 6,
      2 => 7,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '20(?:(?:240|30[67])\\d|6(?:00[0-2]|30[0-4]))\\d{3}|(?:20(?:[017]\\d|2[5-9]|3[1-4]|5[0-4]|6[15-9])|[34]\\d{3})\\d{5}',
    'ExampleNumber' => '312345678',
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
      1 => 6,
      2 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '72(?:[48]0|6[01])\\d{5}|7(?:[015-8]\\d|20|36|4[0-4]|9[89])\\d{6}',
    'ExampleNumber' => '712345678',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800[1-3]\\d{5}',
    'ExampleNumber' => '800123456',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '90[1-3]\\d{6}',
    'ExampleNumber' => '901123456',
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
  'id' => 'UG',
  'countryCode' => 256,
  'internationalPrefix' => '00[057]',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{4})(\\d{5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '202',
        1 => '2024',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{3})(\\d{6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[27-9]|4(?:6[45]|[7-9])',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{2})(\\d{7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[34]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
