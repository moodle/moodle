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
    'NationalNumberPattern' => '[13]\\d{5}',
    'PossibleLength' =>
     [
      0 => 6,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:1(?:06|17|28|39)|3[0-2]\\d)\\d{3}',
    'ExampleNumber' => '106609',
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:14|3[58])\\d{4}',
    'ExampleNumber' => '381234',
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
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
  'id' => 'NF',
  'countryCode' => 672,
  'internationalPrefix' => '00',
  'nationalPrefixForParsing' => '([0-258]\\d{4})$',
  'nationalPrefixTransformRule' => '3$1',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{2})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '1[0-3]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d)(\\d{5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[13]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
