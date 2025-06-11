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
    'NationalNumberPattern' => '[189]\\d{8,9}|[24-6]\\d{8}|[135]\\d{7}',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 9,
      2 => 10,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 6,
      1 => 7,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '13[23]\\d{6}|(?:15|57)\\d{6,7}|(?:2[2-4]|3|4[05-8]|5[05]|6[24-689]|8[2468]|9[235-7])\\d{7}',
    'ExampleNumber' => '234567890',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 9,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 6,
      1 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '1[0-25]\\d{8}',
    'ExampleNumber' => '1001234567',
    'PossibleLength' =>
     [
      0 => 10,
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
    'NationalNumberPattern' => '900\\d{7}',
    'ExampleNumber' => '9001234567',
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
  'id' => 'EG',
  'countryCode' => 20,
  'internationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d)(\\d{7,8})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[23]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{6,7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '1[35]|[4-6]|8[2468]|9[235-7]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '[89]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{2})(\\d{8})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '1',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
