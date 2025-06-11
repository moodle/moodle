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
    'NationalNumberPattern' => '(?:1\\d|9)\\d{9}|[1-9]\\d{7}',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 10,
      2 => 11,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 6,
      1 => 7,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:1[0-6]\\d|99[02-6])\\d{5}|(?:2[13-79]|3[135-8]|4[146-9]|5[135-7]|6[13-9]|7[15-9]|8[1-46-9]|9[1-7])[2-6]\\d{5}',
    'ExampleNumber' => '14567890',
    'PossibleLength' =>
     [
      0 => 8,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 6,
      1 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '9(?:00|6[0-3]|7[024-6]|8[0-24-68])\\d{7}',
    'ExampleNumber' => '9841234567',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '1(?:66001|800\\d\\d)\\d{5}',
    'ExampleNumber' => '16600101234',
    'PossibleLength' =>
     [
      0 => 11,
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
  'id' => 'NP',
  'countryCode' => 977,
  'internationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d)(\\d{7})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '1[2-6]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{6})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '1[01]|[2-8]|9(?:[1-59]|[67][2-6])',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{7})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '9',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{4})(\\d{2})(\\d{5})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '1',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'intlNumberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d)(\\d{7})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '1[2-6]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{6})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '1[01]|[2-8]|9(?:[1-59]|[67][2-6])',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{7})',
      'format' => '$1-$2',
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
  'mobileNumberPortableRegion' => false,
];
