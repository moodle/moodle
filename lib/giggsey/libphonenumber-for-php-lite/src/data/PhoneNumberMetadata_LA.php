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
    'NationalNumberPattern' => '[23]\\d{9}|3\\d{8}|(?:[235-8]\\d|41)\\d{6}',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 9,
      2 => 10,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 6,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:2[13]|[35-7][14]|41|8[1468])\\d{6}',
    'ExampleNumber' => '21212862',
    'PossibleLength' =>
     [
      0 => 8,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 6,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:20(?:[2359]\\d|7[6-8]|88)|302\\d)\\d{6}',
    'ExampleNumber' => '2023123456',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'tollFree' =>
   [
    'PossibleLength' =>
     [
      0 => -1,
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
    'NationalNumberPattern' => '30[013-9]\\d{6}',
    'ExampleNumber' => '301234567',
    'PossibleLength' =>
     [
      0 => 9,
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
  'id' => 'LA',
  'countryCode' => 856,
  'internationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '2[13]|3[14]|[4-8]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{2})(\\d{2})(\\d{3})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '30[013-9]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{2})(\\d{2})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '[23]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
