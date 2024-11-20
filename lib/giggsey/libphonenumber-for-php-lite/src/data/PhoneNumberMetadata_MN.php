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
    'NationalNumberPattern' => '[12]\\d{7,9}|[5-9]\\d{7}',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 9,
      2 => 10,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 4,
      1 => 5,
      2 => 6,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '[12]2[1-3]\\d{5,6}|(?:(?:[12](?:1|27)|5[368])\\d\\d|7(?:0(?:[0-5]\\d|7[078]|80)|128))\\d{4}|[12](?:3[2-8]|4[2-68]|5[1-4689])\\d{6,7}',
    'ExampleNumber' => '53123456',
    'PossibleLengthLocalOnly' =>
     [
      0 => 4,
      1 => 5,
      2 => 6,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:83[01]|92[039])\\d{5}|(?:5[05]|6[069]|8[015689]|9[013-9])\\d{6}',
    'ExampleNumber' => '88123456',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'tollFree' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'premiumRate' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
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
    'NationalNumberPattern' => '712[0-79]\\d{4}|7(?:1[013-9]|[25-9]\\d)\\d{5}',
    'ExampleNumber' => '75123456',
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
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'id' => 'MN',
  'countryCode' => 976,
  'internationalPrefix' => '001',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{2})(\\d{2})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '[12]1',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{4})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[5-9]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{5,6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[12]2[1-3]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{4})(\\d{5,6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[12](?:27|3[2-8]|4[2-68]|5[1-4689])',
        1 => '[12](?:27|3[2-8]|4[2-68]|5[1-4689])[0-3]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{5})(\\d{4,5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[12]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
