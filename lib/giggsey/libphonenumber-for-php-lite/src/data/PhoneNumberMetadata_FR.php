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
    'NationalNumberPattern' => '[1-9]\\d{8}',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:26[013-9]|59[1-35-9])\\d{6}|(?:[13]\\d|2[0-57-9]|4[1-9]|5[0-8])\\d{7}',
    'ExampleNumber' => '123456789',
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:6(?:[0-24-8]\\d|3[0-8]|9[589])|7[3-9]\\d)\\d{6}',
    'ExampleNumber' => '612345678',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '80[0-5]\\d{6}',
    'ExampleNumber' => '801234567',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '836(?:0[0-36-9]|[1-9]\\d)\\d{4}|8(?:1[2-9]|2[2-47-9]|3[0-57-9]|[569]\\d|8[0-35-9])\\d{6}',
    'ExampleNumber' => '891123456',
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '8(?:1[01]|2[0156]|4[02]|84)\\d{6}',
    'ExampleNumber' => '884012345',
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
    'NationalNumberPattern' => '9\\d{8}',
    'ExampleNumber' => '912345678',
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
    'NationalNumberPattern' => '80[6-9]\\d{6}',
    'ExampleNumber' => '806123456',
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
  'id' => 'FR',
  'countryCode' => 33,
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
        0 => '10',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{3})(\\d{3})',
      'format' => '$1 $2',
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
      'pattern' => '(\\d{3})(\\d{2})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '8',
      ],
      'nationalPrefixFormattingRule' => '0 $1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d)(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4 $5',
      'leadingDigitsPatterns' =>
       [
        0 => '[1-79]',
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
      'pattern' => '(\\d{3})(\\d{2})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '8',
      ],
      'nationalPrefixFormattingRule' => '0 $1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d)(\\d{2})(\\d{2})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4 $5',
      'leadingDigitsPatterns' =>
       [
        0 => '[1-79]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
