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
    'NationalNumberPattern' => '[0-57-9]\\d{8}',
    'PossibleLength' =>
     [
      0 => 9,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 3,
      1 => 5,
      2 => 6,
      3 => 7,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:3(?:1[3-5]|2[245]|3[12]|4[24-7]|5[25]|72)|4(?:46|74|87))\\d{6}',
    'ExampleNumber' => '372123456',
    'PossibleLengthLocalOnly' =>
     [
      0 => 3,
      1 => 5,
      2 => 6,
      3 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:4(?:1[18]|4[02-479])|81[1-9])\\d{6}|(?:0[0-57-9]|1[017]|2[02]|[34]0|5[05]|7[0178]|8[078]|9\\d)\\d{7}',
    'ExampleNumber' => '917123456',
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
  'id' => 'TJ',
  'countryCode' => 992,
  'internationalPrefix' => '810',
  'preferredInternationalPrefix' => '8~10',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{6})(\\d)(\\d{2})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '331',
        1 => '3317',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{3})(\\d{2})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '44[02-479]|[34]7',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{4})(\\d)(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '3[1-5]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '[0-57-9]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
