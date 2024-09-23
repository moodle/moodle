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
    'NationalNumberPattern' => '(?:001800|[2-57]|[689]\\d)\\d{7}|1\\d{7,9}',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 9,
      2 => 10,
      3 => 13,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:1[0689]|2\\d|3[2-9]|4[2-5]|5[2-6]|7[3-7])\\d{6}',
    'ExampleNumber' => '21234567',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '67(?:1[0-8]|2[4-7])\\d{5}|(?:14|6[1-6]|[89]\\d)\\d{7}',
    'ExampleNumber' => '812345678',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '(?:001800\\d|1800)\\d{6}',
    'ExampleNumber' => '1800123456',
    'PossibleLength' =>
     [
      0 => 10,
      1 => 13,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '1900\\d{6}',
    'ExampleNumber' => '1900123456',
    'PossibleLength' =>
     [
      0 => 10,
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
    'NationalNumberPattern' => '6[08]\\d{7}',
    'ExampleNumber' => '601234567',
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
  'id' => 'TH',
  'countryCode' => 66,
  'internationalPrefix' => '00[1-9]',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d)(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '2',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{3,4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '[13-9]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{4})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '1',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
