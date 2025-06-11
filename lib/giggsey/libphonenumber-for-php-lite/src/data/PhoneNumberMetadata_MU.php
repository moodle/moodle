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
    'NationalNumberPattern' => '(?:[57]|8\\d\\d)\\d{7}|[2-468]\\d{6}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
      2 => 10,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:2(?:[0346-8]\\d|1[0-7])|4(?:[013568]\\d|2[4-8])|54(?:[3-5]\\d|71)|6\\d\\d|8(?:14|3[129]))\\d{4}',
    'ExampleNumber' => '54480123',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '5(?:4(?:2[1-389]|7[1-9])|87[15-8])\\d{4}|(?:5(?:2[5-9]|4[3-689]|[57]\\d|8[0-689]|9[0-8])|7(?:0[0-3]|3[013]))\\d{5}',
    'ExampleNumber' => '52512345',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '802\\d{7}|80[0-2]\\d{4}',
    'ExampleNumber' => '8001234',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 10,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '30\\d{5}',
    'ExampleNumber' => '3012345',
    'PossibleLength' =>
     [
      0 => 7,
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
    'NationalNumberPattern' => '3(?:20|9\\d)\\d{4}',
    'ExampleNumber' => '3201234',
    'PossibleLength' =>
     [
      0 => 7,
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
  'id' => 'MU',
  'countryCode' => 230,
  'internationalPrefix' => '0(?:0|[24-7]0|3[03])',
  'preferredInternationalPrefix' => '020',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[2-46]|8[013]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{4})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[57]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{5})(\\d{5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '8',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
