<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Dfareporting;

class CustomFloodlightVariable extends \Google\Model
{
  public const TYPE_U1 = 'U1';
  public const TYPE_U2 = 'U2';
  public const TYPE_U3 = 'U3';
  public const TYPE_U4 = 'U4';
  public const TYPE_U5 = 'U5';
  public const TYPE_U6 = 'U6';
  public const TYPE_U7 = 'U7';
  public const TYPE_U8 = 'U8';
  public const TYPE_U9 = 'U9';
  public const TYPE_U10 = 'U10';
  public const TYPE_U11 = 'U11';
  public const TYPE_U12 = 'U12';
  public const TYPE_U13 = 'U13';
  public const TYPE_U14 = 'U14';
  public const TYPE_U15 = 'U15';
  public const TYPE_U16 = 'U16';
  public const TYPE_U17 = 'U17';
  public const TYPE_U18 = 'U18';
  public const TYPE_U19 = 'U19';
  public const TYPE_U20 = 'U20';
  public const TYPE_U21 = 'U21';
  public const TYPE_U22 = 'U22';
  public const TYPE_U23 = 'U23';
  public const TYPE_U24 = 'U24';
  public const TYPE_U25 = 'U25';
  public const TYPE_U26 = 'U26';
  public const TYPE_U27 = 'U27';
  public const TYPE_U28 = 'U28';
  public const TYPE_U29 = 'U29';
  public const TYPE_U30 = 'U30';
  public const TYPE_U31 = 'U31';
  public const TYPE_U32 = 'U32';
  public const TYPE_U33 = 'U33';
  public const TYPE_U34 = 'U34';
  public const TYPE_U35 = 'U35';
  public const TYPE_U36 = 'U36';
  public const TYPE_U37 = 'U37';
  public const TYPE_U38 = 'U38';
  public const TYPE_U39 = 'U39';
  public const TYPE_U40 = 'U40';
  public const TYPE_U41 = 'U41';
  public const TYPE_U42 = 'U42';
  public const TYPE_U43 = 'U43';
  public const TYPE_U44 = 'U44';
  public const TYPE_U45 = 'U45';
  public const TYPE_U46 = 'U46';
  public const TYPE_U47 = 'U47';
  public const TYPE_U48 = 'U48';
  public const TYPE_U49 = 'U49';
  public const TYPE_U50 = 'U50';
  public const TYPE_U51 = 'U51';
  public const TYPE_U52 = 'U52';
  public const TYPE_U53 = 'U53';
  public const TYPE_U54 = 'U54';
  public const TYPE_U55 = 'U55';
  public const TYPE_U56 = 'U56';
  public const TYPE_U57 = 'U57';
  public const TYPE_U58 = 'U58';
  public const TYPE_U59 = 'U59';
  public const TYPE_U60 = 'U60';
  public const TYPE_U61 = 'U61';
  public const TYPE_U62 = 'U62';
  public const TYPE_U63 = 'U63';
  public const TYPE_U64 = 'U64';
  public const TYPE_U65 = 'U65';
  public const TYPE_U66 = 'U66';
  public const TYPE_U67 = 'U67';
  public const TYPE_U68 = 'U68';
  public const TYPE_U69 = 'U69';
  public const TYPE_U70 = 'U70';
  public const TYPE_U71 = 'U71';
  public const TYPE_U72 = 'U72';
  public const TYPE_U73 = 'U73';
  public const TYPE_U74 = 'U74';
  public const TYPE_U75 = 'U75';
  public const TYPE_U76 = 'U76';
  public const TYPE_U77 = 'U77';
  public const TYPE_U78 = 'U78';
  public const TYPE_U79 = 'U79';
  public const TYPE_U80 = 'U80';
  public const TYPE_U81 = 'U81';
  public const TYPE_U82 = 'U82';
  public const TYPE_U83 = 'U83';
  public const TYPE_U84 = 'U84';
  public const TYPE_U85 = 'U85';
  public const TYPE_U86 = 'U86';
  public const TYPE_U87 = 'U87';
  public const TYPE_U88 = 'U88';
  public const TYPE_U89 = 'U89';
  public const TYPE_U90 = 'U90';
  public const TYPE_U91 = 'U91';
  public const TYPE_U92 = 'U92';
  public const TYPE_U93 = 'U93';
  public const TYPE_U94 = 'U94';
  public const TYPE_U95 = 'U95';
  public const TYPE_U96 = 'U96';
  public const TYPE_U97 = 'U97';
  public const TYPE_U98 = 'U98';
  public const TYPE_U99 = 'U99';
  public const TYPE_U100 = 'U100';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#customFloodlightVariable".
   *
   * @var string
   */
  public $kind;
  /**
   * The type of custom floodlight variable to supply a value for. These map to
   * the "u[1-100]=" in the tags.
   *
   * @var string
   */
  public $type;
  /**
   * The value of the custom floodlight variable. The length of string must not
   * exceed 100 characters.
   *
   * @var string
   */
  public $value;

  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#customFloodlightVariable".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The type of custom floodlight variable to supply a value for. These map to
   * the "u[1-100]=" in the tags.
   *
   * Accepted values: U1, U2, U3, U4, U5, U6, U7, U8, U9, U10, U11, U12, U13,
   * U14, U15, U16, U17, U18, U19, U20, U21, U22, U23, U24, U25, U26, U27, U28,
   * U29, U30, U31, U32, U33, U34, U35, U36, U37, U38, U39, U40, U41, U42, U43,
   * U44, U45, U46, U47, U48, U49, U50, U51, U52, U53, U54, U55, U56, U57, U58,
   * U59, U60, U61, U62, U63, U64, U65, U66, U67, U68, U69, U70, U71, U72, U73,
   * U74, U75, U76, U77, U78, U79, U80, U81, U82, U83, U84, U85, U86, U87, U88,
   * U89, U90, U91, U92, U93, U94, U95, U96, U97, U98, U99, U100
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The value of the custom floodlight variable. The length of string must not
   * exceed 100 characters.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomFloodlightVariable::class, 'Google_Service_Dfareporting_CustomFloodlightVariable');
