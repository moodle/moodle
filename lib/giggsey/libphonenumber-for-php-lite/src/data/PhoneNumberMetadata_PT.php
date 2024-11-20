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
    'NationalNumberPattern' => '1693\\d{5}|(?:[26-9]\\d|30)\\d{7}',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '2(?:[12]\\d|3[1-689]|4[1-59]|[57][1-9]|6[1-35689]|8[1-69]|9[1256])\\d{6}',
    'ExampleNumber' => '212345678',
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '6(?:[06]92(?:30|9\\d)|[35]92(?:3[034]|9\\d))\\d{3}|(?:(?:16|6[0356])93|9(?:[1-36]\\d\\d|480))\\d{5}',
    'ExampleNumber' => '912345678',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '80[02]\\d{6}',
    'ExampleNumber' => '800123456',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '(?:6(?:0[178]|4[68])\\d|76(?:0[1-57]|1[2-47]|2[237]))\\d{5}',
    'ExampleNumber' => '760123456',
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '80(?:8\\d|9[1579])\\d{5}',
    'ExampleNumber' => '808123456',
  ],
  'personalNumber' =>
   [
    'NationalNumberPattern' => '884[0-4689]\\d{5}',
    'ExampleNumber' => '884123456',
  ],
  'voip' =>
   [
    'NationalNumberPattern' => '30\\d{7}',
    'ExampleNumber' => '301234567',
  ],
  'pager' =>
   [
    'NationalNumberPattern' => '6(?:222\\d|8988)\\d{4}',
    'ExampleNumber' => '622212345',
  ],
  'uan' =>
   [
    'NationalNumberPattern' => '70(?:38[01]|596|(?:7\\d|8[17])\\d)\\d{4}',
    'ExampleNumber' => '707123456',
  ],
  'voicemail' =>
   [
    'NationalNumberPattern' => '600\\d{6}|6[06]923[34]\\d{3}',
    'ExampleNumber' => '600110000',
  ],
  'noInternationalDialling' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'id' => 'PT',
  'countryCode' => 351,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '2[12]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '16|[236-9]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
