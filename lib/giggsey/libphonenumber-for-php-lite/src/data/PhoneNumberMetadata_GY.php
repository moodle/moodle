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
    'NationalNumberPattern' => '(?:[2-8]\\d{3}|9008)\\d{3}',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:2(?:1[6-9]|2[0-35-9]|3[1-4]|5[3-9]|6\\d|7[0-79])|3(?:2[25-9]|3\\d)|4(?:4[0-24]|5[56])|50[0-6]|77[1-57])\\d{4}',
    'ExampleNumber' => '2201234',
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:510|6\\d\\d|7(?:[01]\\d|2[156]|31|49))\\d{4}',
    'ExampleNumber' => '6091234',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '(?:289|8(?:00|6[28]|88|99))\\d{4}',
    'ExampleNumber' => '2891234',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '9008\\d{3}',
    'ExampleNumber' => '9008123',
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
    'NationalNumberPattern' => '515\\d{4}',
    'ExampleNumber' => '5151234',
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
  'id' => 'GY',
  'countryCode' => 592,
  'internationalPrefix' => '001',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-9]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
