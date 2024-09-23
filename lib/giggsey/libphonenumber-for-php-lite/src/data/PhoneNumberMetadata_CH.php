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
    'NationalNumberPattern' => '8\\d{11}|[2-9]\\d{8}',
    'PossibleLength' =>
     [
      0 => 9,
      1 => 12,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:2[12467]|3[1-4]|4[134]|5[256]|6[12]|[7-9]1)\\d{7}',
    'ExampleNumber' => '212345678',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '7[35-9]\\d{7}',
    'ExampleNumber' => '781234567',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800\\d{6}',
    'ExampleNumber' => '800123456',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '90[016]\\d{6}',
    'ExampleNumber' => '900123456',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '84[0248]\\d{6}',
    'ExampleNumber' => '840123456',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'personalNumber' =>
   [
    'NationalNumberPattern' => '878\\d{6}',
    'ExampleNumber' => '878123456',
    'PossibleLength' =>
     [
      0 => 9,
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
    'NationalNumberPattern' => '74[0248]\\d{6}',
    'ExampleNumber' => '740123456',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'uan' =>
   [
    'NationalNumberPattern' => '5[18]\\d{7}',
    'ExampleNumber' => '581234567',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'voicemail' =>
   [
    'NationalNumberPattern' => '860\\d{9}',
    'ExampleNumber' => '860123456789',
    'PossibleLength' =>
     [
      0 => 12,
    ],
  ],
  'noInternationalDialling' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'id' => 'CH',
  'countryCode' => 41,
  'internationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '8[047]|90',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-79]|81',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{2})(\\d{3})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4 $5',
      'leadingDigitsPatterns' =>
       [
        0 => '8',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
