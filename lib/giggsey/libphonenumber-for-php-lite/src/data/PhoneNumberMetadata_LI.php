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
    'NationalNumberPattern' => '[68]\\d{8}|(?:[2378]\\d|90)\\d{5}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 9,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:2(?:01|1[27]|2[02]|3\\d|6[02-578]|96)|3(?:[24]0|33|7[0135-7]|8[048]|9[0269]))\\d{4}',
    'ExampleNumber' => '2345678',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:6(?:(?:4[5-9]|5[0-469])\\d|6(?:[024-6]\\d|[17]0|3[7-9]))\\d|7(?:[37-9]\\d|42|56))\\d{4}',
    'ExampleNumber' => '660234567',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '8002[28]\\d\\d|80(?:05\\d|9)\\d{4}',
    'ExampleNumber' => '8002222',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '90(?:02[258]|1(?:23|3[14])|66[136])\\d\\d',
    'ExampleNumber' => '9002222',
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
    'NationalNumberPattern' => '870(?:28|87)\\d\\d',
    'ExampleNumber' => '8702812',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'voicemail' =>
   [
    'NationalNumberPattern' => '697(?:42|56|[78]\\d)\\d{4}',
    'ExampleNumber' => '697861234',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'noInternationalDialling' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'id' => 'LI',
  'countryCode' => 423,
  'internationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '(1001)|0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '[2379]|8(?:0[09]|7)',
        1 => '[2379]|8(?:0(?:02|9)|7)',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '$CC $1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '8',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '69',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '$CC $1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '6',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '$CC $1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
