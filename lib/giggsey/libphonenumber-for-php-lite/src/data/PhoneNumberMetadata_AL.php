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
    'NationalNumberPattern' => '(?:700\\d\\d|900)\\d{3}|8\\d{5,7}|(?:[2-5]|6\\d)\\d{7}',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 7,
      2 => 8,
      3 => 9,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '4505[0-2]\\d{3}|(?:[2358][16-9]\\d[2-9]|4410)\\d{4}|(?:[2358][2-5][2-9]|4(?:[2-57-9][2-9]|6\\d))\\d{5}',
    'ExampleNumber' => '22345678',
    'PossibleLength' =>
     [
      0 => 8,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
      1 => 6,
      2 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '6(?:[78][2-9]|9\\d)\\d{6}',
    'ExampleNumber' => '672123456',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800\\d{4}',
    'ExampleNumber' => '8001234',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '900[1-9]\\d\\d',
    'ExampleNumber' => '900123',
    'PossibleLength' =>
     [
      0 => 6,
    ],
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '808[1-9]\\d\\d',
    'ExampleNumber' => '808123',
    'PossibleLength' =>
     [
      0 => 6,
    ],
  ],
  'personalNumber' =>
   [
    'NationalNumberPattern' => '700[2-9]\\d{4}',
    'ExampleNumber' => '70021234',
    'PossibleLength' =>
     [
      0 => 8,
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
  'id' => 'AL',
  'countryCode' => 355,
  'internationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{3,4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '80|9',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d)(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '4[2-6]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '[2358][2-5]|4',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{3})(\\d{5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[23578]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '6',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
