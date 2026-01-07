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

namespace Google\Service\DisplayVideo;

class LocationList extends \Google\Model
{
  /**
   * Default value when type is not specified or is unknown.
   */
  public const LOCATION_TYPE_TARGETING_LOCATION_TYPE_UNSPECIFIED = 'TARGETING_LOCATION_TYPE_UNSPECIFIED';
  /**
   * The type for proximity geo location.
   */
  public const LOCATION_TYPE_TARGETING_LOCATION_TYPE_PROXIMITY = 'TARGETING_LOCATION_TYPE_PROXIMITY';
  /**
   * The type for regional geo location.
   */
  public const LOCATION_TYPE_TARGETING_LOCATION_TYPE_REGIONAL = 'TARGETING_LOCATION_TYPE_REGIONAL';
  /**
   * Required. Immutable. The unique ID of the advertiser the location list
   * belongs to.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * Required. The display name of the location list. Must be UTF-8 encoded with
   * a maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The unique ID of the location list. Assigned by the system.
   *
   * @var string
   */
  public $locationListId;
  /**
   * Required. Immutable. The type of location. All locations in the list will
   * share this type.
   *
   * @var string
   */
  public $locationType;
  /**
   * Output only. The resource name of the location list.
   *
   * @var string
   */
  public $name;

  /**
   * Required. Immutable. The unique ID of the advertiser the location list
   * belongs to.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Required. The display name of the location list. Must be UTF-8 encoded with
   * a maximum size of 240 bytes.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The unique ID of the location list. Assigned by the system.
   *
   * @param string $locationListId
   */
  public function setLocationListId($locationListId)
  {
    $this->locationListId = $locationListId;
  }
  /**
   * @return string
   */
  public function getLocationListId()
  {
    return $this->locationListId;
  }
  /**
   * Required. Immutable. The type of location. All locations in the list will
   * share this type.
   *
   * Accepted values: TARGETING_LOCATION_TYPE_UNSPECIFIED,
   * TARGETING_LOCATION_TYPE_PROXIMITY, TARGETING_LOCATION_TYPE_REGIONAL
   *
   * @param self::LOCATION_TYPE_* $locationType
   */
  public function setLocationType($locationType)
  {
    $this->locationType = $locationType;
  }
  /**
   * @return self::LOCATION_TYPE_*
   */
  public function getLocationType()
  {
    return $this->locationType;
  }
  /**
   * Output only. The resource name of the location list.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationList::class, 'Google_Service_DisplayVideo_LocationList');
