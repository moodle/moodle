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

namespace Google\Service\Directory;

class UserLocation extends \Google\Model
{
  /**
   * Textual location. This is most useful for display purposes to concisely
   * describe the location. For example 'Mountain View, CA', 'Near Seattle',
   * 'US-NYC-9TH 9A209A.''
   *
   * @var string
   */
  public $area;
  /**
   * Building Identifier.
   *
   * @var string
   */
  public $buildingId;
  /**
   * Custom Type.
   *
   * @var string
   */
  public $customType;
  /**
   * Most specific textual code of individual desk location.
   *
   * @var string
   */
  public $deskCode;
  /**
   * Floor name/number.
   *
   * @var string
   */
  public $floorName;
  /**
   * Floor section. More specific location within the floor. For example if a
   * floor is divided into sections 'A', 'B' and 'C' this field would identify
   * one of those values.
   *
   * @var string
   */
  public $floorSection;
  /**
   * Each entry can have a type which indicates standard types of that entry.
   * For example location could be of types default and desk. In addition to
   * standard type an entry can have a custom type and can give it any name.
   * Such types should have 'custom' as type and also have a customType value.
   *
   * @var string
   */
  public $type;

  /**
   * Textual location. This is most useful for display purposes to concisely
   * describe the location. For example 'Mountain View, CA', 'Near Seattle',
   * 'US-NYC-9TH 9A209A.''
   *
   * @param string $area
   */
  public function setArea($area)
  {
    $this->area = $area;
  }
  /**
   * @return string
   */
  public function getArea()
  {
    return $this->area;
  }
  /**
   * Building Identifier.
   *
   * @param string $buildingId
   */
  public function setBuildingId($buildingId)
  {
    $this->buildingId = $buildingId;
  }
  /**
   * @return string
   */
  public function getBuildingId()
  {
    return $this->buildingId;
  }
  /**
   * Custom Type.
   *
   * @param string $customType
   */
  public function setCustomType($customType)
  {
    $this->customType = $customType;
  }
  /**
   * @return string
   */
  public function getCustomType()
  {
    return $this->customType;
  }
  /**
   * Most specific textual code of individual desk location.
   *
   * @param string $deskCode
   */
  public function setDeskCode($deskCode)
  {
    $this->deskCode = $deskCode;
  }
  /**
   * @return string
   */
  public function getDeskCode()
  {
    return $this->deskCode;
  }
  /**
   * Floor name/number.
   *
   * @param string $floorName
   */
  public function setFloorName($floorName)
  {
    $this->floorName = $floorName;
  }
  /**
   * @return string
   */
  public function getFloorName()
  {
    return $this->floorName;
  }
  /**
   * Floor section. More specific location within the floor. For example if a
   * floor is divided into sections 'A', 'B' and 'C' this field would identify
   * one of those values.
   *
   * @param string $floorSection
   */
  public function setFloorSection($floorSection)
  {
    $this->floorSection = $floorSection;
  }
  /**
   * @return string
   */
  public function getFloorSection()
  {
    return $this->floorSection;
  }
  /**
   * Each entry can have a type which indicates standard types of that entry.
   * For example location could be of types default and desk. In addition to
   * standard type an entry can have a custom type and can give it any name.
   * Such types should have 'custom' as type and also have a customType value.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserLocation::class, 'Google_Service_Directory_UserLocation');
