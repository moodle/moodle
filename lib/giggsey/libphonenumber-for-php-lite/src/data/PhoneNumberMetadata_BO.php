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
    'NationalNumberPattern' => '(?:[2-467]\\d\\d|8001)\\d{5}',
    'PossibleLength' =>
     [
      0 => 8,
      1 => 9,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 7,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:2(?:2\\d\\d|5(?:11|[258]\\d|9[67])|6(?:12|2\\d|9[34])|8(?:2[34]|39|62))|3(?:3\\d\\d|4(?:6\\d|8[24])|8(?:25|42|5[257]|86|9[25])|9(?:[27]\\d|3[2-4]|4[248]|5[24]|6[2-6]))|4(?:4\\d\\d|6(?:11|[24689]\\d|72)))\\d{4}',
    'ExampleNumber' => '22123456',
    'PossibleLength' =>
     [
      0 => 8,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '[67]\\d{7}',
    'ExampleNumber' => '71234567',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '8001[07]\\d{4}',
    'ExampleNumber' => '800171234',
    'PossibleLength' =>
     [
      0 => 9,
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
    'NationalNumberPattern' => '8001[07]\\d{4}',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'id' => 'BO',
  'countryCode' => 591,
  'internationalPrefix' => '00(?:1\\d)?',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0(1\\d)?',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d)(\\d{7})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[23]|4[46]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '0$CC $1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{8})',
      'format' => '$1',
      'leadingDigitsPatterns' =>
       [
        0 => '[67]',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '0$CC $1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{2})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '8',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '0$CC $1',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
