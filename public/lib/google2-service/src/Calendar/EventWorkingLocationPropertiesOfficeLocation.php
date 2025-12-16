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

namespace Google\Service\Calendar;

class EventWorkingLocationPropertiesOfficeLocation extends \Google\Model
{
  /**
   * An optional building identifier. This should reference a building ID in the
   * organization's Resources database.
   *
   * @var string
   */
  public $buildingId;
  /**
   * An optional desk identifier.
   *
   * @var string
   */
  public $deskId;
  /**
   * An optional floor identifier.
   *
   * @var string
   */
  public $floorId;
  /**
   * An optional floor section identifier.
   *
   * @var string
   */
  public $floorSectionId;
  /**
   * The office name that's displayed in Calendar Web and Mobile clients. We
   * recommend you reference a building name in the organization's Resources
   * database.
   *
   * @var string
   */
  public $label;

  /**
   * An optional building identifier. This should reference a building ID in the
   * organization's Resources database.
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
   * An optional desk identifier.
   *
   * @param string $deskId
   */
  public function setDeskId($deskId)
  {
    $this->deskId = $deskId;
  }
  /**
   * @return string
   */
  public function getDeskId()
  {
    return $this->deskId;
  }
  /**
   * An optional floor identifier.
   *
   * @param string $floorId
   */
  public function setFloorId($floorId)
  {
    $this->floorId = $floorId;
  }
  /**
   * @return string
   */
  public function getFloorId()
  {
    return $this->floorId;
  }
  /**
   * An optional floor section identifier.
   *
   * @param string $floorSectionId
   */
  public function setFloorSectionId($floorSectionId)
  {
    $this->floorSectionId = $floorSectionId;
  }
  /**
   * @return string
   */
  public function getFloorSectionId()
  {
    return $this->floorSectionId;
  }
  /**
   * The office name that's displayed in Calendar Web and Mobile clients. We
   * recommend you reference a building name in the organization's Resources
   * database.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventWorkingLocationPropertiesOfficeLocation::class, 'Google_Service_Calendar_EventWorkingLocationPropertiesOfficeLocation');
