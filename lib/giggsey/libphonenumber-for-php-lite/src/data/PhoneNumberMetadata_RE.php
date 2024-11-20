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
    'NationalNumberPattern' => '(?:26|[689]\\d)\\d{7}',
    'PossibleLength' =>
     [
      0 => 9,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '26(?:2\\d\\d|3(?:0\\d|1[0-6]))\\d{4}',
    'ExampleNumber' => '262161234',
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '69(?:2\\d\\d|3(?:[06][0-6]|1[013]|2[0-2]|3[0-39]|4\\d|5[0-5]|7[0-37]|8[0-8]|9[0-479]))\\d{4}',
    'ExampleNumber' => '692123456',
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '80\\d{7}',
    'ExampleNumber' => '801234567',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '89[1-37-9]\\d{6}',
    'ExampleNumber' => '891123456',
  ],
  'sharedCost' =>
   [
    'NationalNumberPattern' => '8(?:1[019]|2[0156]|84|90)\\d{6}',
    'ExampleNumber' => '810123456',
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
    'NationalNumberPattern' => '9(?:399[0-3]|479[0-5]|76(?:2[27]|3[0-37]))\\d{4}',
    'ExampleNumber' => '939901234',
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
  'id' => 'RE',
  'countryCode' => 262,
  'internationalPrefix' => '00',
  'nationalPrefix' => '0',
  'nationalPrefixForParsing' => '0',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
    0 =>
     [
      'pattern' => '(\\d{3})(\\d{2})(\\d{2})(\\d{2})',
      'format' => '$1 $2 $3 $4',
      'leadingDigitsPatterns' =>
       [
        0 => '[2689]',
      ],
      'nationalPrefixFormattingRule' => '0$1',
      'domesticCarrierCodeFormattingRule' => '',
      'nationalPrefixOptionalWhenFormatting' => false,
    ],
  ],
  'mainCountryForCode' => true,
  'mobileNumberPortableRegion' => false,
];
