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

namespace Google\Service\PeopleService;

class Location extends \Google\Model
{
  /**
   * The building identifier.
   *
   * @var string
   */
  public $buildingId;
  /**
   * Whether the location is the current location.
   *
   * @var bool
   */
  public $current;
  /**
   * The individual desk location.
   *
   * @var string
   */
  public $deskCode;
  /**
   * The floor name or number.
   *
   * @var string
   */
  public $floor;
  /**
   * The floor section in `floor_name`.
   *
   * @var string
   */
  public $floorSection;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The type of the location. The type can be custom or one of these predefined
   * values: * `desk` * `grewUp`
   *
   * @var string
   */
  public $type;
  /**
   * The free-form value of the location.
   *
   * @var string
   */
  public $value;

  /**
   * The building identifier.
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
   * Whether the location is the current location.
   *
   * @param bool $current
   */
  public function setCurrent($current)
  {
    $this->current = $current;
  }
  /**
   * @return bool
   */
  public function getCurrent()
  {
    return $this->current;
  }
  /**
   * The individual desk location.
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
   * The floor name or number.
   *
   * @param string $floor
   */
  public function setFloor($floor)
  {
    $this->floor = $floor;
  }
  /**
   * @return string
   */
  public function getFloor()
  {
    return $this->floor;
  }
  /**
   * The floor section in `floor_name`.
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
   * Metadata about the location.
   *
   * @param FieldMetadata $metadata
   */
  public function setMetadata(FieldMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return FieldMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The type of the location. The type can be custom or one of these predefined
   * values: * `desk` * `grewUp`
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
  /**
   * The free-form value of the location.
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
class_alias(Location::class, 'Google_Service_PeopleService_Location');
