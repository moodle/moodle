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
    'NationalNumberPattern' => '(?:050|[2-57-9]\\d\\d)\\d{3}',
    'PossibleLength' =>
     [
      0 => 6,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:2[03-9]|3[0-5]|4[1-7]|88)\\d{4}',
    'ExampleNumber' => '201234',
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:5[0-4]|[79]\\d|8[0-79])\\d{4}',
    'ExampleNumber' => '751234',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '050\\d{3}',
    'ExampleNumber' => '050012',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '36\\d{4}',
    'ExampleNumber' => '366711',
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
  'id' => 'NC',
  'countryCode' => 687,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})',
      'format' => '$1',
      'leadingDigitsPatterns' =>
       [
        0 => '5[6-8]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{2})(\\d{2})(\\d{2})',
      'format' => '$1.$2.$3',
      'leadingDigitsPatterns' =>
       [
        0 => '[02-57-9]',
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
      'pattern' => '(\\d{2})(\\d{2})(\\d{2})',
      'format' => '$1.$2.$3',
      'leadingDigitsPatterns' =>
       [
        0 => '[02-57-9]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
