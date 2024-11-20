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
    'NationalNumberPattern' => '00[1-9]\\d{8,11}|(?:[12]|5\\d{3})\\d{7}|[13-6]\\d{9}|(?:[1-6]\\d|80)\\d{7}|[3-6]\\d{4,5}|(?:00|7)0\\d{8}',
    'PossibleLength' =>
     [
      0 => 5,
      1 => 6,
      2 => 8,
      3 => 9,
      4 => 10,
      5 => 11,
      6 => 12,
      7 => 13,
      8 => 14,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 3,
      1 => 4,
      2 => 7,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:2|3[1-3]|[46][1-4]|5[1-5])[1-9]\\d{6,7}|(?:3[1-3]|[46][1-4]|5[1-5])1\\d{2,3}',
    'ExampleNumber' => '22123456',
    'PossibleLength' =>
     [
      0 => 5,
      1 => 6,
      2 => 8,
      3 => 9,
      4 => 10,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 3,
      1 => 4,
      2 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '1(?:05(?:[0-8]\\d|9[0-6])|22[13]\\d)\\d{4,5}|1(?:0[0-46-9]|[16-9]\\d|2[013-9])\\d{6,7}',
    'ExampleNumber' => '1020000000',
    'PossibleLength' =>
     [
      0 => 9,
      1 => 10,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '00(?:308\\d{6,7}|798\\d{7,9})|(?:00368|80)\\d{7}',
    'ExampleNumber' => '801234567',
    'PossibleLength' =>
     [
      0 => 9,
      1 => 11,
      2 => 12,
      3 => 13,
      4 => 14,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '60[2-9]\\d{6}',
    'ExampleNumber' => '602345678',
    'PossibleLength' =>
     [
      0 => 9,
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
    'NationalNumberPattern' => '50\\d{8,9}',
    'ExampleNumber' => '5012345678',
    'PossibleLength' =>
     [
      0 => 10,
      1 => 11,
    ],
  ],
  'voip' =>
   [
    'NationalNumberPattern' => '70\\d{8}',
    'ExampleNumber' => '7012345678',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'pager' =>
   [
    'NationalNumberPattern' => '15\\d{7,8}',
    'ExampleNumber' => '1523456789',
    'PossibleLength' =>
     [
      0 => 9,
      1 => 10,
    ],
  ],
  'uan' =>
   [
    'NationalNumberPattern' => '1(?:5(?:22|33|44|66|77|88|99)|6(?:[07]0|44|6[0168]|88)|8(?:00|33|55|77|99))\\d{4}',
    'ExampleNumber' => '15441234',
    'PossibleLength' =>
     [
      0 => 8,
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
    'NationalNumberPattern' => '00(?:3(?:08\\d{6,7}|68\\d{7})|798\\d{7,9})',
    'PossibleLength' =>
     [
      0 => 11,
      1 => 12,
      2 => 13,
      3 => 14,
    ],
  ],
  'id' => 'KR',
  'countryCode' => 82,
  'internationalPrefix' => '00(?:[125689]|3(?:[46]5|91)|7(?:00|27|3|55|6[126]))',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0(8(?:[1-46-8]|5\\d\\d))?',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{5})',
      'format' => '$1',
      'leadingDigitsPatterns' =>
       [
        0 => '1[016-9]1',
        1 => '1[016-9]11',
        2 => '1[016-9]114',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{3,4})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '(?:3[1-3]|[46][1-4]|5[1-5])1',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{4})(\\d{4})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '1',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d)(\\d{3,4})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '2',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '60|8',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    5 =>
     [
      'pattern' => '(\\d{2})(\\d{3,4})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '[1346]|5[1-5]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    6 =>
     [
      'pattern' => '(\\d{2})(\\d{4})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '[57]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    7 =>
     [
      'pattern' => '(\\d{5})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '003',
        1 => '0030',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    8 =>
     [
      'pattern' => '(\\d{2})(\\d{5})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '5',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    9 =>
     [
      'pattern' => '(\\d{5})(\\d{3,4})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '0',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    10 =>
     [
      'pattern' => '(\\d{5})(\\d{2})(\\d{3})(\\d{4})',
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
      'pattern' => '(\\d{2})(\\d{3,4})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '(?:3[1-3]|[46][1-4]|5[1-5])1',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{4})(\\d{4})',
      'format' => '$1-$2',
      'leadingDigitsPatterns' =>
       [
        0 => '1',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d)(\\d{3,4})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '2',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '60|8',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{2})(\\d{3,4})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '[1346]|5[1-5]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    5 =>
     [
      'pattern' => '(\\d{2})(\\d{4})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '[57]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    6 =>
     [
      'pattern' => '(\\d{2})(\\d{5})(\\d{4})',
      'format' => '$1-$2-$3',
      'leadingDigitsPatterns' =>
       [
        0 => '5',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '0$CC-$1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
