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
    'NationalNumberPattern' => '59\\d{4,6}|9\\d{5,10}|(?:[2-46-8]\\d|5[0-8])\\d{4,7}',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 7,
      2 => 8,
      3 => 9,
      4 => 10,
      5 => 11,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:[26]1|3[289]|4[1246-8]|7[1-3]|8[1-36])\\d{5,7}|(?:2(?:2[4-68]|[4-68]\\d|7[15]|9[1-5])|3(?:18|3[167]|4[2357]|51|[67]\\d)|4(?:3[12]|5[13]|9[1-47])|5(?:[1-4]\\d|5[02-4])|6(?:3[1-3]|44|7[1-8])|7(?:4[0-4]|5\\d|6[1-578]|75|8[0-8])|858)\\d{5,6}',
    'ExampleNumber' => '212345678',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
      2 => 9,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
      1 => 6,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '9(?:51|6[129]|7[1-6]|8[1-7]|9[1-5])\\d{6}',
    'ExampleNumber' => '961456789',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '9800\\d{5,7}',
    'ExampleNumber' => '98000123456',
    'PossibleLength' =>
     [
      0 => 9,
      1 => 10,
      2 => 11,
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
    'NationalNumberPattern' => '8700[0-4]\\d{4}',
    'ExampleNumber' => '870012345',
    'PossibleLength' =>
     [
      0 => 9,
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
    'NationalNumberPattern' => '[2-9]0\\d{4,7}',
    'ExampleNumber' => '201234567',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 7,
      2 => 8,
      3 => 9,
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
  'id' => 'PY',
  'countryCode' => 595,
  'internationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{3,6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-9]0',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[26]1|3[289]|4[1246-8]|7[1-3]|8[1-36]',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{4,5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '2[279]|3[13-5]|4[359]|5|6(?:[34]|7[1-46-8])|7[46-8]|85',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{3,4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '2[14-68]|3[26-9]|4[1246-8]|6(?:1|75)|7[1-35]|8[1-36]',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '87',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    5 =>
     [
      'pattern' => '(\\d{3})(\\d{6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '9(?:[5-79]|8[1-7])',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    6 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-8]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    7 =>
     [
      'pattern' => '(\\d{4})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '9',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
