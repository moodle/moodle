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
    'NationalNumberPattern' => '(?:38\\d|[4-9])\\d{6}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 9,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:4(?:1[0-24-69]|2[0-7]|[37][0-8]|4[0-24589]|5[0-68]|6\\d|8[0-36-8])|5(?:05|[156]\\d|2[02578]|3[0-579]|4[03-7]|7[0-2578]|8[0-35-9]|9[013-689])|872)\\d{4}',
    'ExampleNumber' => '4101234',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:38[589]\\d\\d|6(?:1[1-8]|2[0-6]|3[026-9]|4[014679]|5[0159]|6[0-69]|70|8[06-8]|9\\d)|7(?:5[057]|[6-9]\\d)|8(?:2[0-59]|[3-69]\\d|8[238]))\\d{4}',
    'ExampleNumber' => '6111234',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '80[0-8]\\d{4}',
    'ExampleNumber' => '8001234',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '90(?:0\\d|1[5-79]|2[015-79]|3[135-79]|4[125-7]|5[25-79]|7[1-37]|8[0-35-7])\\d{3}',
    'ExampleNumber' => '9001234',
    'PossibleLength' =>
     [
      0 => 7,
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
    'NationalNumberPattern' => '49[0-24-79]\\d{4}',
    'ExampleNumber' => '4921234',
    'PossibleLength' =>
     [
      0 => 7,
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
    'NationalNumberPattern' => '809\\d{4}',
    'ExampleNumber' => '8091234',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'voicemail' =>
   [
    'NationalNumberPattern' => '(?:689|8(?:7[18]|80)|95[48])\\d{4}',
    'ExampleNumber' => '6891234',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'noInternationalDialling' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'id' => 'IS',
  'countryCode' => 354,
  'internationalPrefix' => '00|1(?:0(?:01|[12]0)|100)',
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
        0 => '[4-9]',
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
        0 => '3',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
