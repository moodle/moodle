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
    'NationalNumberPattern' => '(?:[279]\\d|[58]0)\\d{6}',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '2[2-6]\\d{6}',
    'ExampleNumber' => '22345678',
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '9(?:10|[4-79]\\d)\\d{5}',
    'ExampleNumber' => '96123456',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800\\d{5}',
    'ExampleNumber' => '80001234',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '90[09]\\d{5}',
    'ExampleNumber' => '90012345',
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '80[1-9]\\d{5}',
    'ExampleNumber' => '80112345',
  ],
  'personalNumber' =>
   [
    'NationalNumberPattern' => '700\\d{5}',
    'ExampleNumber' => '70012345',
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
    'NationalNumberPattern' => '(?:50|77)\\d{6}',
    'ExampleNumber' => '77123456',
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
  'id' => 'CY',
  'countryCode' => 357,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{2})(\\d{6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[257-9]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
