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
    'NationalNumberPattern' => '(?:(?:[2689]|7\\d)\\d|32|53)\\d{6}',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 9,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '87(?:000|90[01])\\d{3}|(?:2(?:6(?:2[0-35-9]|3[0-578]|4[24-7]|5[0-24-8]|[6-8][023]|9[0-3])|7(?:0[1-79]|10|2[014-7]|3[0-689]|4[019]|5[0-3578]))|32(?:0[1-69]|1[1-35-7]|2[024-7]|3\\d|4[0-3]|[5-7][023])|53(?:0[0-3]|[13][023]|2[0-59]|49|5[0-35-9]|6[15]|7[45]|8[1-6]|9[0-36-9])|6(?:2(?:[05]0|22)|3(?:00|33)|4(?:0[0-25]|1[2-7]|2[0569]|[38][07-9]|4[025689]|6[0-589]|7\\d|9[0-2])|5(?:[01][056]|2[034]|3[0-57-9]|4[178]|5[0-69]|6[0-35-9]|7[1-379]|8[0-68]|9[0239]))|87(?:20|7[078]|99))\\d{4}',
    'ExampleNumber' => '62001234',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '7(?:[78][0-25-9]|9\\d)\\d{6}',
    'ExampleNumber' => '790123456',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '80\\d{6}',
    'ExampleNumber' => '80012345',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '9\\d{7}',
    'ExampleNumber' => '90012345',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '85\\d{6}',
    'ExampleNumber' => '85012345',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'personalNumber' =>
   [
    'NationalNumberPattern' => '70\\d{7}',
    'ExampleNumber' => '700123456',
    'PossibleLength' =>
     [
      0 => 9,
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
    'NationalNumberPattern' => '74(?:66|77)\\d{5}',
    'ExampleNumber' => '746612345',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'uan' =>
   [
    'NationalNumberPattern' => '8(?:10|8\\d)\\d{5}',
    'ExampleNumber' => '88101234',
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
    'PossibleLength' =>
     [
      0 => -1,
    ],
  ],
  'id' => 'JO',
  'countryCode' => 962,
  'internationalPrefix' => '00',
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
        0 => '[2356]|87',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{3})(\\d{5,6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[89]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{2})(\\d{7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '70',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d)(\\d{4})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '7',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
