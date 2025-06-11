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
    'NationalNumberPattern' => '(?:[2-489]\\d|55)\\d{6}',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '2(?:2\\d|5[1-5]|81|9[149])\\d{5}',
    'ExampleNumber' => '22453300',
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:[34]\\d|55)\\d{6}',
    'ExampleNumber' => '34101234',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '8\\d{7}',
    'ExampleNumber' => '80012345',
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
    'NationalNumberPattern' => '9(?:[67][0-4]|8[0-3589]|9\\d)\\d{5}',
    'ExampleNumber' => '98901234',
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
  'id' => 'HT',
  'countryCode' => 509,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{2})(\\d{2})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-589]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
