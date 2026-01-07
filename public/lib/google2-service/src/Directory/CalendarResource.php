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

class CalendarResource extends \Google\Model
{
  /**
   * Unique ID for the building a resource is located in.
   *
   * @var string
   */
  public $buildingId;
  /**
   * Capacity of a resource, number of seats in a room.
   *
   * @var int
   */
  public $capacity;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etags;
  /**
   * Instances of features for the calendar resource.
   *
   * @var array
   */
  public $featureInstances;
  /**
   * Name of the floor a resource is located on.
   *
   * @var string
   */
  public $floorName;
  /**
   * Name of the section within a floor a resource is located in.
   *
   * @var string
   */
  public $floorSection;
  /**
   * The read-only auto-generated name of the calendar resource which includes
   * metadata about the resource such as building name, floor, capacity, etc.
   * For example, "NYC-2-Training Room 1A (16)".
   *
   * @var string
   */
  public $generatedResourceName;
  /**
   * The type of the resource. For calendar resources, the value is
   * `admin#directory#resources#calendars#CalendarResource`.
   *
   * @var string
   */
  public $kind;
  /**
   * The category of the calendar resource. Either CONFERENCE_ROOM or OTHER.
   * Legacy data is set to CATEGORY_UNKNOWN.
   *
   * @var string
   */
  public $resourceCategory;
  /**
   * Description of the resource, visible only to admins.
   *
   * @var string
   */
  public $resourceDescription;
  /**
   * The read-only email for the calendar resource. Generated as part of
   * creating a new calendar resource.
   *
   * @var string
   */
  public $resourceEmail;
  /**
   * The unique ID for the calendar resource.
   *
   * @var string
   */
  public $resourceId;
  /**
   * The name of the calendar resource. For example, "Training Room 1A".
   *
   * @var string
   */
  public $resourceName;
  /**
   * The type of the calendar resource, intended for non-room resources.
   *
   * @var string
   */
  public $resourceType;
  /**
   * Description of the resource, visible to users and admins.
   *
   * @var string
   */
  public $userVisibleDescription;

  /**
   * Unique ID for the building a resource is located in.
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
   * Capacity of a resource, number of seats in a room.
   *
   * @param int $capacity
   */
  public function setCapacity($capacity)
  {
    $this->capacity = $capacity;
  }
  /**
   * @return int
   */
  public function getCapacity()
  {
    return $this->capacity;
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
   * Instances of features for the calendar resource.
   *
   * @param array $featureInstances
   */
  public function setFeatureInstances($featureInstances)
  {
    $this->featureInstances = $featureInstances;
  }
  /**
   * @return array
   */
  public function getFeatureInstances()
  {
    return $this->featureInstances;
  }
  /**
   * Name of the floor a resource is located on.
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
   * Name of the section within a floor a resource is located in.
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
   * The read-only auto-generated name of the calendar resource which includes
   * metadata about the resource such as building name, floor, capacity, etc.
   * For example, "NYC-2-Training Room 1A (16)".
   *
   * @param string $generatedResourceName
   */
  public function setGeneratedResourceName($generatedResourceName)
  {
    $this->generatedResourceName = $generatedResourceName;
  }
  /**
   * @return string
   */
  public function getGeneratedResourceName()
  {
    return $this->generatedResourceName;
  }
  /**
   * The type of the resource. For calendar resources, the value is
   * `admin#directory#resources#calendars#CalendarResource`.
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
   * The category of the calendar resource. Either CONFERENCE_ROOM or OTHER.
   * Legacy data is set to CATEGORY_UNKNOWN.
   *
   * @param string $resourceCategory
   */
  public function setResourceCategory($resourceCategory)
  {
    $this->resourceCategory = $resourceCategory;
  }
  /**
   * @return string
   */
  public function getResourceCategory()
  {
    return $this->resourceCategory;
  }
  /**
   * Description of the resource, visible only to admins.
   *
   * @param string $resourceDescription
   */
  public function setResourceDescription($resourceDescription)
  {
    $this->resourceDescription = $resourceDescription;
  }
  /**
   * @return string
   */
  public function getResourceDescription()
  {
    return $this->resourceDescription;
  }
  /**
   * The read-only email for the calendar resource. Generated as part of
   * creating a new calendar resource.
   *
   * @param string $resourceEmail
   */
  public function setResourceEmail($resourceEmail)
  {
    $this->resourceEmail = $resourceEmail;
  }
  /**
   * @return string
   */
  public function getResourceEmail()
  {
    return $this->resourceEmail;
  }
  /**
   * The unique ID for the calendar resource.
   *
   * @param string $resourceId
   */
  public function setResourceId($resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return string
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * The name of the calendar resource. For example, "Training Room 1A".
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * The type of the calendar resource, intended for non-room resources.
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * Description of the resource, visible to users and admins.
   *
   * @param string $userVisibleDescription
   */
  public function setUserVisibleDescription($userVisibleDescription)
  {
    $this->userVisibleDescription = $userVisibleDescription;
  }
  /**
   * @return string
   */
  public function getUserVisibleDescription()
  {
    return $this->userVisibleDescription;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CalendarResource::class, 'Google_Service_Directory_CalendarResource');
