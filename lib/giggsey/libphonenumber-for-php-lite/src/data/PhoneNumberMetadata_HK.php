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
    'NationalNumberPattern' => '8[0-46-9]\\d{6,7}|9\\d{4,7}|(?:[2-7]|9\\d{3})\\d{7}',
    'PossibleLength' =>
     [
      0 => 5,
      1 => 6,
      2 => 7,
      3 => 8,
      4 => 9,
      5 => 11,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:2(?:[13-9]\\d|2[013-9])\\d|3(?:(?:[1569][0-24-9]|4[0-246-9]|7[0-24-69])\\d|8(?:4[0-8]|[59]\\d|6[01]))|58(?:0[1-9]|1[2-9]))\\d{4}',
    'ExampleNumber' => '21234567',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '(?:4(?:44[0-25-9]|6(?:1[0-7]|4[0-57-9]|6[0-4]))|5(?:73[0-6]|95[0-8])|6(?:26[013-8]|66[0-3])|70(?:7[1-8]|8[0-4])|848[0-35-9]|9(?:29[013-9]|39[01]|59[0-4]|899))\\d{4}|(?:4(?:4[0-35-8]|6[02357-9])|5(?:[1-59][0-46-9]|6[0-4689]|7[0-246-9])|6(?:0[1-9]|[13-59]\\d|[268][0-57-9]|7[0-79])|70[129]|84[0-29]|9(?:0[1-9]|1[02-9]|[2358][0-8]|[467]\\d))\\d{5}',
    'ExampleNumber' => '51234567',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '800\\d{6}',
    'ExampleNumber' => '800123456',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '900(?:[0-24-9]\\d{7}|3\\d{1,4})',
    'ExampleNumber' => '90012345678',
    'PossibleLength' =>
     [
      0 => 5,
      1 => 6,
      2 => 7,
      3 => 8,
      4 => 11,
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
    'NationalNumberPattern' => '8(?:1[0-4679]\\d|2(?:[0-36]\\d|7[0-4])|3(?:[034]\\d|2[09]|70))\\d{4}',
    'ExampleNumber' => '81123456',
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
    'NationalNumberPattern' => '7(?:1(?:0[0-38]|1[0-3679]|3[013]|69|9[0136])|2(?:[02389]\\d|1[18]|7[27-9])|3(?:[0-38]\\d|7[0-369]|9[2357-9])|47\\d|5(?:[178]\\d|5[0-5])|6(?:0[0-7]|2[236-9]|[35]\\d)|7(?:[27]\\d|8[7-9])|8(?:[23689]\\d|7[1-9])|9(?:[025]\\d|6[0-246-8]|7[0-36-9]|8[238]))\\d{4}',
    'ExampleNumber' => '71123456',
    'PossibleLength' =>
     [
      0 => 8,
    ],
  ],
  'uan' =>
   [
    'NationalNumberPattern' => '30(?:0[1-9]|[15-7]\\d|2[047]|89)\\d{4}',
    'ExampleNumber' => '30161234',
    'PossibleLength' =>
     [
      0 => 8,
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
  'id' => 'HK',
  'countryCode' => 852,
  'internationalPrefix' => '00(?:30|5[09]|[126-9]?)',
  'preferredInternationalPrefix' => '00',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{2,5})',
      'format' => '$1 $2',
      'leadingDigitsPatterns' =>
       [
        0 => '900',
        1 => '9003',
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
        0 => '[2-7]|8[1-4]|9(?:0[1-9]|[1-8])',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    2 =>
     [
      'pattern' => '(\\d{3})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3',
      'leadingDigitsPatterns' =>
       [
        0 => '8',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
    3 =>
     [
      'pattern' => '(\\d{3})(\\d{2})(\\d{3})(\\d{3})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '9',
      ],
      'nationalPrefixFormattingRule' => '',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => false,
  'mobileNumberPortableRegion' => true,
];
