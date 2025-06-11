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
    'NationalNumberPattern' => '8\\d{9}|[4578]\\d{7}|(?:[3-8]\\d|90)\\d{5}',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
      2 => 10,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:3[23589]|4[3-8]|6\\d|7[1-9]|88)\\d{5}',
    'ExampleNumber' => '3212345',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:5\\d{5}|8(?:1(?:0(?:0(?:00|[178]\\d)|[3-9]\\d\\d)|(?:1(?:0[236]|1\\d)|(?:2[0-59]|[3-79]\\d)\\d)\\d)|2(?:0(?:0(?:00|4\\d)|(?:19|[2-7]\\d)\\d)|(?:(?:[124-6]\\d|3[5-9])\\d|7(?:[0-79]\\d|8[13-9])|8(?:[2-6]\\d|7[01]))\\d)|[349]\\d{4}))\\d\\d|5(?:(?:[02]\\d|5[0-478])\\d|1(?:[0-8]\\d|95)|6(?:4[0-4]|5[1-589]))\\d{3}',
    'ExampleNumber' => '51234567',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800(?:(?:0\\d\\d|1)\\d|[2-9])\\d{3}',
    'ExampleNumber' => '80012345',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '(?:40\\d\\d|900)\\d{4}',
    'ExampleNumber' => '9001234',
    'PossibleLength' =>
     [
      0 => 7,
      1 => 8,
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
    'NationalNumberPattern' => '70[0-2]\\d{5}',
    'ExampleNumber' => '70012345',
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
    'NationalNumberPattern' => '800[2-9]\\d{3}',
    'PossibleLength' =>
     [
      0 => 7,
    ],
  ],
  'id' => 'EE',
  'countryCode' => 372,
  'internationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[369]|4[3-8]|5(?:[0-2]|5[0-478]|6[45])|7[1-9]|88',
        1 => '[369]|4[3-8]|5(?:[02]|1(?:[0-8]|95)|5[0-478]|6(?:4[0-4]|5[1-589]))|7[1-9]|88',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    1 =>
     [
      'pattern' => '(\\d{4})(\\d{3,4})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '[45]|8(?:00|[1-49])',
        1 => '[45]|8(?:00[1-9]|[1-49])',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{2})(\\d{2})(\\d{4})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '7',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{4})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
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
  'mobileNumberPortableRegion' => true,
];
