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
    'NationalNumberPattern' => '1\\d{3,12}|2\\d{6,12}|43(?:(?:0\\d|5[02-9])\\d{3,9}|2\\d{4,5}|[3467]\\d{4}|8\\d{4,6}|9\\d{4,7})|5\\d{4,12}|8\\d{7,12}|9\\d{8,12}|(?:[367]\\d|4[0-24-9])\\d{4,11}',
    'PossibleLength' =>
     [
      0 => 4,
      1 => 5,
      2 => 6,
      3 => 7,
      4 => 8,
      5 => 9,
      6 => 10,
      7 => 11,
      8 => 12,
      9 => 13,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 3,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '1(?:11\\d|[2-9]\\d{3,11})|(?:316|463|(?:51|66|73)2)\\d{3,10}|(?:2(?:1[467]|2[13-8]|5[2357]|6[1-46-8]|7[1-8]|8[124-7]|9[1458])|3(?:1[1-578]|3[23568]|4[5-7]|5[1378]|6[1-38]|8[3-68])|4(?:2[1-8]|35|7[1368]|8[2457])|5(?:2[1-8]|3[357]|4[147]|5[12578]|6[37])|6(?:13|2[1-47]|4[135-8]|5[468])|7(?:2[1-8]|35|4[13478]|5[68]|6[16-8]|7[1-6]|9[45]))\\d{4,10}',
    'ExampleNumber' => '1234567890',
    'PossibleLengthLocalOnly' =>
     [
      0 => 3,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '6(?:5[0-3579]|6[013-9]|[7-9]\\d)\\d{4,10}',
    'ExampleNumber' => '664123456',
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
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800\\d{6,10}',
    'ExampleNumber' => '800123456',
    'PossibleLength' =>
     [
      0 => 9,
      1 => 10,
      2 => 11,
      3 => 12,
      4 => 13,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '(?:8[69][2-68]|9(?:0[01]|3[019]))\\d{6,10}',
    'ExampleNumber' => '900123456',
    'PossibleLength' =>
     [
      0 => 9,
      1 => 10,
      2 => 11,
      3 => 12,
      4 => 13,
    ],
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '8(?:10|2[018])\\d{6,10}|828\\d{5}',
    'ExampleNumber' => '810123456',
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
  'personalNumber' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'voip' =>
   [
    'NationalNumberPattern' => '5(?:0[1-9]|17|[79]\\d)\\d{2,10}|7[28]0\\d{6,10}',
    'ExampleNumber' => '780123456',
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
      8 => 13,
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
  'id' => 'AT',
  'countryCode' => 43,
  'internationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{4})',
      'format' => '$1',
      'leadingDigitsPatterns' =>
       [
        0 => '14',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d)(\\d{3,12})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '1(?:11|[2-9])',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{2})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '517',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{2})(\\d{3,5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '5[079]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{6})',
      'format' => '$1',
      'leadingDigitsPatterns' =>
       [
        0 => '[18]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    5 =>
     [
      'pattern' => '(\\d{3})(\\d{3,10})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '(?:31|4)6|51|6(?:5[0-3579]|[6-9])|7(?:20|32|8)|[89]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    6 =>
     [
      'pattern' => '(\\d{4})(\\d{3,9})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-467]|5[2-6]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    7 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{3,4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '5',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    8 =>
     [
      'pattern' => '(\\d{2})(\\d{4})(\\d{4,7})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '5',
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
      'pattern' => '(\\d)(\\d{3,12})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '1(?:11|[2-9])',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{3})(\\d{2})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '517',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{2})(\\d{3,5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '5[079]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{3})(\\d{3,10})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '(?:31|4)6|51|6(?:5[0-3579]|[6-9])|7(?:20|32|8)|[89]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{4})(\\d{3,9})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-467]|5[2-6]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    5 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{3,4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '5',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    6 =>
     [
      'pattern' => '(\\d{2})(\\d{4})(\\d{4,7})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '5',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
