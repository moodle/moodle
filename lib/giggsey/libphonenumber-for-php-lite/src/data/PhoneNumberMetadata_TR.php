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
    'NationalNumberPattern' => '4\\d{6}|8\\d{11,12}|(?:[2-58]\\d\\d|900)\\d{7}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 10,
      2 => 12,
      3 => 13,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:2(?:[13][26]|[28][2468]|[45][268]|[67][246])|3(?:[13][28]|[24-6][2468]|[78][02468]|92)|4(?:[16][246]|[23578][2468]|4[26]))\\d{7}',
    'ExampleNumber' => '2123456789',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '561(?:011|61\\d)\\d{4}|5(?:0[15-7]|1[06]|24|[34]\\d|5[1-59]|9[46])\\d{7}',
    'ExampleNumber' => '5012345678',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '8(?:00\\d{7}(?:\\d{2,3})?|11\\d{7})',
    'ExampleNumber' => '8001234567',
    'PossibleLength' =>
     [
      0 => 10,
      1 => 12,
      2 => 13,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '(?:8[89]8|900)\\d{7}',
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
    'NationalNumberPattern' => '592(?:21[12]|461)\\d{4}',
    'ExampleNumber' => '5922121234',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'voip' =>
   [
    'NationalNumberPattern' => '850\\d{7}',
    'ExampleNumber' => '8500123456',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'pager' =>
   [
    'NationalNumberPattern' => '512\\d{7}',
    'ExampleNumber' => '5123456789',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'uan' =>
   [
    'NationalNumberPattern' => '444\\d{4}',
    'ExampleNumber' => '4441444',
    'PossibleLength' =>
     [
      0 => 7,
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
    'NationalNumberPattern' => '(?:444|811\\d{3})\\d{4}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 10,
    ],
  ],
  'id' => 'TR',
  'countryCode' => 90,
  'internationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d)(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '444',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => true,
    ],
    1 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '512|8[01589]|90',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => true,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '5(?:[0-59]|61)',
        1 => '5(?:[0-59]|61[06])',
        2 => '5(?:[0-59]|61[06]1)',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => true,
    ],
    3 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '[24][1-8]|3[1-9]',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => true,
    ],
    4 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{6,7})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '80',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => true,
    ],
  ],
  'intlNumberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '512|8[01589]|90',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => true,
    ],
    1 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '5(?:[0-59]|61)',
        1 => '5(?:[0-59]|61[06])',
        2 => '5(?:[0-59]|61[06]1)',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => true,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '[24][1-8]|3[1-9]',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => true,
    ],
    3 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{6,7})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '80',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => true,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
