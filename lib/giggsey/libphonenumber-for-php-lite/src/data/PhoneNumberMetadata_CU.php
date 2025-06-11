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
    'NationalNumberPattern' => '[27]\\d{6,7}|[34]\\d{5,7}|63\\d{6}|(?:5|8\\d\\d)\\d{7}',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 7,
      2 => 8,
      3 => 10,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 4,
      1 => 5,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:3[23]|4[89])\\d{4,6}|(?:31|4[36]|8(?:0[25]|78)\\d)\\d{6}|(?:2[1-4]|4[1257]|7\\d)\\d{5,6}',
    'ExampleNumber' => '71234567',
    'PossibleLengthLocalOnly' =>
     [
      0 => 4,
      1 => 5,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:5\\d|63)\\d{6}',
    'ExampleNumber' => '51234567',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800\\d{7}',
    'ExampleNumber' => '8001234567',
    'PossibleLength' =>
     [
      0 => 10,
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
    'NationalNumberPattern' => '807\\d{7}',
    'ExampleNumber' => '8071234567',
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
  'id' => 'CU',
  'countryCode' => 53,
  'internationalPrefix' => '119',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{2})(\\d{4,6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '2[1-4]|[34]',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d)(\\d{6,7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '7',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d)(\\d{7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[56]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{3})(\\d{7})',
      'format' => '$1 $2',
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
