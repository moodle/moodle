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
    'NationalNumberPattern' => '(?:[2-7]|9\\d)\\d{8}|2\\d{5}|(?:1800|8)\\d{7,9}',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 8,
      2 => 9,
      3 => 10,
      4 => 11,
      5 => 12,
      6 => 13,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 4,
      1 => 5,
      2 => 7,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:(?:2[3-8]|3[2-68]|4[2-9]|5[2-6]|6[2-58]|7[24578])\\d{3}|88(?:22\\d\\d|42))\\d{4}|(?:2|8[2-8]\\d\\d)\\d{5}',
    'ExampleNumber' => '232345678',
    'PossibleLength' =>
     [
      0 => 6,
      1 => 8,
      2 => 9,
      3 => 10,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 4,
      1 => 5,
      2 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:8(?:1[37]|9[5-8])|9(?:0[5-9]|1[0-24-9]|[235-7]\\d|4[2-9]|8[135-9]|9[1-9]))\\d{7}',
    'ExampleNumber' => '9051234567',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '1800\\d{7,9}',
    'ExampleNumber' => '180012345678',
    'PossibleLength' =>
     [
      0 => 11,
      1 => 12,
      2 => 13,
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
  'id' => 'PH',
  'countryCode' => 63,
  'internationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d)(\\d{5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '2',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{4})(\\d{4,6})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '3(?:23|39|46)|4(?:2[3-6]|[35]9|4[26]|76)|544|88[245]|(?:52|64|86)2',
        1 => '3(?:230|397|461)|4(?:2(?:35|[46]4|51)|396|4(?:22|63)|59[347]|76[15])|5(?:221|446)|642[23]|8(?:622|8(?:[24]2|5[13]))',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{5})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '346|4(?:27|9[35])|883',
        1 => '3469|4(?:279|9(?:30|56))|8834',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d)(\\d{4})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '2',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    4 =>
     [
      'pattern' => '(\\d{2})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '[3-7]|8[2-8]',
      ],
      'nationalPrefixFormattingRule' => '(0$1)',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    5 =>
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
    6 =>
     [
      'pattern' => '(\\d{4})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '1',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    7 =>
     [
      'pattern' => '(\\d{4})(\\d{1,2})(\\d{3})(\\d{4})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '1',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => false,
];
