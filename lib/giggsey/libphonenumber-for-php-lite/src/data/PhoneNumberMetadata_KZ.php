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
    'NationalNumberPattern' => '(?:33622|8\\d{8})\\d{5}|[78]\\d{9}',
    'PossibleLength' =>
     [
      0 => 10,
      1 => 14,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
      1 => 6,
      2 => 7,
    ],
  ],
  'fixedLine' =>
   [
    'NationalNumberPattern' => '(?:33622|7(?:1(?:0(?:[23]\\d|4[0-3]|59|63)|1(?:[23]\\d|4[0-79]|59)|2(?:[23]\\d|59)|3(?:2\\d|3[0-79]|4[0-35-9]|59)|4(?:[24]\\d|3[013-9]|5[1-9]|97)|5(?:2\\d|3[1-9]|4[0-7]|59)|6(?:[2-4]\\d|5[19]|61)|72\\d|8(?:[27]\\d|3[1-46-9]|4[0-5]|59))|2(?:1(?:[23]\\d|4[46-9]|5[3469])|2(?:2\\d|3[0679]|46|5[12679])|3(?:[2-4]\\d|5[139])|4(?:2\\d|3[1-35-9]|59)|5(?:[23]\\d|4[0-8]|59|61)|6(?:2\\d|3[1-9]|4[0-4]|59)|7(?:[2379]\\d|40|5[279])|8(?:[23]\\d|4[0-3]|59)|9(?:2\\d|3[124578]|59))))\\d{5}',
    'ExampleNumber' => '7123456789',
    'PossibleLength' =>
     [
      0 => 10,
    ],
    'PossibleLengthLocalOnly' =>
     [
      0 => 5,
      1 => 6,
      2 => 7,
    ],
  ],
  'mobile' =>
   [
    'NationalNumberPattern' => '7(?:0[0-25-8]|47|6[0-4]|7[15-8]|85)\\d{7}',
    'ExampleNumber' => '7710009998',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'tollFree' =>
   [
    'NationalNumberPattern' => '8(?:00|108\\d{3})\\d{7}',
    'ExampleNumber' => '8001234567',
  ],
  'premiumRate' =>
   [
    'NationalNumberPattern' => '809\\d{7}',
    'ExampleNumber' => '8091234567',
    'PossibleLength' =>
     [
      0 => 10,
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
    'NationalNumberPattern' => '808\\d{7}',
    'ExampleNumber' => '8081234567',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'voip' =>
   [
    'NationalNumberPattern' => '751\\d{7}',
    'ExampleNumber' => '7511234567',
    'PossibleLength' =>
     [
      0 => 10,
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
    'NationalNumberPattern' => '751\\d{7}',
    'PossibleLength' =>
     [
      0 => 10,
    ],
  ],
  'id' => 'KZ',
  'countryCode' => 7,
  'internationalPrefix' => '810',
  'preferredInternationalPrefix' => '8~10',
  'nationalPrefix' => '8',
  'nationalPrefixForParsing' => '8',
  'sameMobileAndFixedLinePattern' => false,
  'numberFormat' =>
   [
  ],
  'mainCountryForCode' => false,
  'leadingDigits' => '33|7',
  'mobileNumberPortableRegion' => true,
];
