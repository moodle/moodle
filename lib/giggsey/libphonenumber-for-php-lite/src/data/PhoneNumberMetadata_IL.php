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
    'NationalNumberPattern' => '1\\d{6}(?:\\d{3,5})?|[57]\\d{8}|[1-489]\\d{7}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
      2 => 9,
      3 => 10,
      4 => 11,
      5 => 12,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '153\\d{8,9}|29[1-9]\\d{5}|(?:2[0-8]|[3489]\\d)\\d{6}',
    'ExampleNumber' => '21234567',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 11,
      2 => 12,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '55410\\d{4}|5(?:(?:[02][02-9]|[149][2-9]|[36]\\d|8[3-7])\\d|5(?:01|2\\d|3[0-3]|4[34]|5[0-25689]|6[6-8]|7[0-267]|8[7-9]|9[1-9]))\\d{5}',
    'ExampleNumber' => '502345678',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '1(?:255|80[019]\\d{3})\\d{3}',
    'ExampleNumber' => '1800123456',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 10,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '1212\\d{4}|1(?:200|9(?:0[0-2]|19))\\d{6}',
    'ExampleNumber' => '1919123456',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 10,
    ],
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '1700\\d{6}',
    'ExampleNumber' => '1700123456',
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
    'NationalNumberPattern' => '7(?:38(?:0\\d|5[09]|88)|8(?:33|55|77|81)\\d)\\d{4}|7(?:18|2[23]|3[237]|47|6[258]|7\\d|82|9[2-9])\\d{6}',
    'ExampleNumber' => '771234567',
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
    'NationalNumberPattern' => '1599\\d{6}',
    'ExampleNumber' => '1599123456',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'voicemail' =>
   [
    'NationalNumberPattern' => '151\\d{8,9}',
    'ExampleNumber' => '15112340000',
    'PossibleLength' =>
     [
      0 => 11,
      1 => 12,
    ],
  ],
  'noInternationalDialling' =>
   [
    'NationalNumberPattern' => '1700\\d{6}',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'id' => 'IL',
  'countryCode' => 972,
  'internationalPrefix' => '0(?:0|1[2-9])',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{4})(\\d{3})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '125',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{4})(\\d{2})(\\d{2})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '121',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d)(\\d{3})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-489]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '[57]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{4})(\\d{3})(\\d{3})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '12',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    5 =>
     [
      'pattern' => '(\\d{4})(\\d{6})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '159',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    6 =>
     [
      'pattern' => '(\\d)(\\d{3})(\\d{3})(\\d{3})',
      'format' => '$1-$2-$3-$4',
      'leadingDigitsPatterns' =>
       [
        0 => '1[7-9]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    7 =>
     [
      'pattern' => '(\\d{3})(\\d{1,2})(\\d{3})(\\d{4})',
      'format' => '$1-$2 $3-$4',
      'leadingDigitsPatterns' =>
       [
        0 => '15',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
