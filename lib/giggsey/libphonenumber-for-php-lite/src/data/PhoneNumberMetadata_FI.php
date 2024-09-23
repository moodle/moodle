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
    'NationalNumberPattern' => '[1-35689]\\d{4}|7\\d{10,11}|(?:[124-7]\\d|3[0-46-9])\\d{8}|[1-9]\\d{5,8}',
    'PossibleLength' =>
     [
      0 => 5,
      1 => 6,
      2 => 7,
      3 => 8,
      4 => 9,
      5 => 10,
      6 => 11,
      7 => 12,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:1[3-79][1-8]|[23568][1-8]\\d|9(?:00|[1-8]\\d))\\d{2,6}',
    'ExampleNumber' => '131234567',
    'PossibleLength' =>
     [
      0 => 5,
      1 => 6,
      2 => 7,
      3 => 8,
      4 => 9,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '4946\\d{2,6}|(?:4[0-8]|50)\\d{4,8}',
    'ExampleNumber' => '412345678',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 7,
      2 => 8,
      3 => 9,
      4 => 10,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800\\d{4,6}',
    'ExampleNumber' => '800123456',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
      2 => 9,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '[67]00\\d{5,6}',
    'ExampleNumber' => '600123456',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 9,
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
    'NationalNumberPattern' => '20\\d{4,8}|60[12]\\d{5,6}|7(?:099\\d{4,5}|5[03-9]\\d{3,7})|20[2-59]\\d\\d|(?:606|7(?:0[78]|1|3\\d))\\d{7}|(?:10|29|3[09]|70[1-5]\\d)\\d{4,8}',
    'ExampleNumber' => '10112345',
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
    'NationalNumberPattern' => '20(?:2[023]|9[89])\\d{1,6}|(?:60[12]\\d|7099)\\d{4,5}|(?:606|7(?:0[78]|1|3\\d))\\d{7}|(?:[1-3]00|7(?:0[1-5]\\d\\d|5[03-9]))\\d{3,7}',
  ],
  'id' => 'FI',
  'countryCode' => 358,
  'internationalPrefix' => '00|99(?:[01469]|5(?:[14]1|3[23]|5[59]|77|88|9[09]))',
  'preferredInternationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{5})',
      'format' => '$1',
      'leadingDigitsPatterns' =>
       [
        0 => '75[12]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{5})',
      'format' => '$1',
      'leadingDigitsPatterns' =>
       [
        0 => '20[2-59]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{6})',
      'format' => '$1',
      'leadingDigitsPatterns' =>
       [
        0 => '11',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{3})(\\d{3,7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '(?:[1-3]0|[68])0|70[07-9]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{2})(\\d{4,8})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[14]|2[09]|50|7[135]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    5 =>
     [
      'pattern' => '(\\d{2})(\\d{6,10})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '7',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    6 =>
     [
      'pattern' => '(\\d)(\\d{4,9})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '(?:1[3-79]|[2568])[1-8]|3(?:0[1-9]|[1-9])|9',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'intlNumberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{5})',
      'format' => '$1',
      'leadingDigitsPatterns' =>
       [
        0 => '20[2-59]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{3})(\\d{3,7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '(?:[1-3]0|[68])0|70[07-9]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{2})(\\d{4,8})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[14]|2[09]|50|7[135]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{2})(\\d{6,10})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '7',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d)(\\d{4,9})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '(?:1[3-79]|[2568])[1-8]|3(?:0[1-9]|[1-9])|9',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => true,
  'leadingDigits' => '1[03-79]|[2-9]',
  'mobileNumberPortableRegion' => true,
];
