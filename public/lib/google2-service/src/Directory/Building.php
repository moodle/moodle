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

class Building extends \Google\Collection
{
  protected $collection_key = 'floorNames';
  protected $addressType = BuildingAddress::class;
  protected $addressDataType = '';
  /**
   * Unique identifier for the building. The maximum length is 100 characters.
   *
   * @var string
   */
  public $buildingId;
  /**
   * The building name as seen by users in Calendar. Must be unique for the
   * customer. For example, "NYC-CHEL". The maximum length is 100 characters.
   *
   * @var string
   */
  public $buildingName;
  protected $coordinatesType = BuildingCoordinates::class;
  protected $coordinatesDataType = '';
  /**
   * A brief description of the building. For example, "Chelsea Market".
   *
   * @var string
   */
  public $description;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etags;
  /**
   * The display names for all floors in this building. The floors are expected
   * to be sorted in ascending order, from lowest floor to highest floor. For
   * example, ["B2", "B1", "L", "1", "2", "2M", "3", "PH"] Must contain at least
   * one entry.
   *
   * @var string[]
   */
  public $floorNames;
  /**
   * Kind of resource this is.
   *
   * @var string
   */
  public $kind;

  /**
   * The postal address of the building. See [`PostalAddress`](/my-
   * business/reference/rest/v4/PostalAddress) for details. Note that only a
   * single address line and region code are required.
   *
   * @param BuildingAddress $address
   */
  public function setAddress(BuildingAddress $address)
  {
    $this->address = $address;
  }
  /**
   * @return BuildingAddress
   */
  public function getAddress()
  {
    return $this->address;
  }
  /**
   * Unique identifier for the building. The maximum length is 100 characters.
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
   * The building name as seen by users in Calendar. Must be unique for the
   * customer. For example, "NYC-CHEL". The maximum length is 100 characters.
   *
   * @param string $buildingName
   */
  public function setBuildingName($buildingName)
  {
    $this->buildingName = $buildingName;
  }
  /**
   * @return string
   */
  public function getBuildingName()
  {
    return $this->buildingName;
  }
  /**
   * The geographic coordinates of the center of the building, expressed as
   * latitude and longitude in decimal degrees.
   *
   * @param BuildingCoordinates $coordinates
   */
  public function setCoordinates(BuildingCoordinates $coordinates)
  {
    $this->coordinates = $coordinates;
  }
  /**
   * @return BuildingCoordinates
   */
  public function getCoordinates()
  {
    return $this->coordinates;
  }
  /**
   * A brief description of the building. For example, "Chelsea Market".
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * ETag of the resource.
   *
   * @param string $etags
   */
  public function setEtags($etags)
  {
    $this->etags = $etags;
  }
  /**
   * @return string
   */
  public function getEtags()
  {
    return $this->etags;
  }
  /**
   * The display names for all floors in this building. The floors are expected
   * to be sorted in ascending order, from lowest floor to highest floor. For
   * example, ["B2", "B1", "L", "1", "2", "2M", "3", "PH"] Must contain at least
   * one entry.
   *
   * @param string[] $floorNames
   */
  public function setFloorNames($floorNames)
  {
    $this->floorNames = $floorNames;
  }
  /**
   * @return string[]
   */
  public function getFloorNames()
  {
    return $this->floorNames;
  }
  /**
   * Kind of resource this is.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Building::class, 'Google_Service_Directory_Building');
