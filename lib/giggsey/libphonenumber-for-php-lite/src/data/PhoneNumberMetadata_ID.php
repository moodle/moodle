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
    'NationalNumberPattern' => '(?:(?:00[1-9]|8\\d)\\d{4}|[1-36])\\d{6}|00\\d{10}|[1-9]\\d{8,10}|[2-9]\\d{7}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
      2 => 9,
      3 => 10,
      4 => 11,
      5 => 12,
      6 => 13,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
      1 => 6,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '2[124]\\d{7,8}|619\\d{8}|2(?:1(?:14|500)|2\\d{3})\\d{3}|61\\d{5,8}|(?:2(?:[35][1-4]|6[0-8]|7[1-6]|8\\d|9[1-8])|3(?:1|[25][1-8]|3[1-68]|4[1-3]|6[1-3568]|7[0-469]|8\\d)|4(?:0[1-589]|1[01347-9]|2[0-36-8]|3[0-24-68]|43|5[1-378]|6[1-5]|7[134]|8[1245])|5(?:1[1-35-9]|2[25-8]|3[124-9]|4[1-3589]|5[1-46]|6[1-8])|6(?:[25]\\d|3[1-69]|4[1-6])|7(?:02|[125][1-9]|[36]\\d|4[1-8]|7[0-36-9])|9(?:0[12]|1[013-8]|2[0-479]|5[125-8]|6[23679]|7[159]|8[01346]))\\d{5,8}',
    'ExampleNumber' => '218350123',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
      2 => 9,
      3 => 10,
      4 => 11,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
      1 => 6,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '8[1-35-9]\\d{7,10}',
    'ExampleNumber' => '812345678',
    'PossibleLength' =>
     [
      0 => 9,
      1 => 10,
      2 => 11,
      3 => 12,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '00[17]803\\d{7}|(?:177\\d|800)\\d{5,7}|001803\\d{6}',
    'ExampleNumber' => '8001234567',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 9,
      2 => 10,
      3 => 11,
      4 => 12,
      5 => 13,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '809\\d{7}',
    'ExampleNumber' => '8091234567',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '804\\d{7}',
    'ExampleNumber' => '8041234567',
    'PossibleLength' =>
     [
      0 => 10,
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
    'NationalNumberPattern' => '(?:1500|8071\\d{3})\\d{3}',
    'ExampleNumber' => '8071123456',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 10,
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
    'NationalNumberPattern' => '001803\\d{6,7}|(?:007803\\d|8071)\\d{6}',
    'PossibleLength' =>
     [
      0 => 10,
      1 => 12,
      2 => 13,
    ],
  ],
  'id' => 'ID',
  'countryCode' => 62,
  'internationalPrefix' => '00[89]',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d)(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '15',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{5,9})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '2[124]|[36]1',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{5,7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '800',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{3})(\\d{5,8})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-79]',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{3})(\\d{3,4})(\\d{3})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '8[1-35-9]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    5 =>
     [
      'pattern' => '(\\d{3})(\\d{6,8})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '1',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    6 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '804',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    7 =>
     [
      'pattern' => '(\\d{3})(\\d)(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '80',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    8 =>
     [
      'pattern' => '(\\d{3})(\\d{4})(\\d{4,5})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '8',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    9 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '001',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    10 =>
     [
      'pattern' => '(\\d{2})(\\d{4})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '0',
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
      'pattern' => '(\\d)(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '15',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{5,9})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '2[124]|[36]1',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{5,7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '800',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{3})(\\d{5,8})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-79]',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{3})(\\d{3,4})(\\d{3})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '8[1-35-9]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    5 =>
     [
      'pattern' => '(\\d{3})(\\d{6,8})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '1',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    6 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '804',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    7 =>
     [
      'pattern' => '(\\d{3})(\\d)(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '80',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    8 =>
     [
      'pattern' => '(\\d{3})(\\d{4})(\\d{4,5})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '8',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
