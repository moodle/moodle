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

namespace Google\Service\FirebaseManagement;

class Location extends \Google\Collection
{
  /**
   * Used internally for distinguishing unset values and is not intended for
   * external use.
   */
  public const TYPE_LOCATION_TYPE_UNSPECIFIED = 'LOCATION_TYPE_UNSPECIFIED';
  /**
   * The location is a regional location. Data in a regional location is
   * replicated in multiple zones within a region.
   */
  public const TYPE_REGIONAL = 'REGIONAL';
  /**
   * The location is a multi-regional location. Data in a multi-region location
   * is replicated in multiple regions. Within each region, data is replicated
   * in multiple zones.
   */
  public const TYPE_MULTI_REGIONAL = 'MULTI_REGIONAL';
  protected $collection_key = 'features';
  /**
   * Products and services that are available in the location for default Google
   * Cloud resources.
   *
   * @var string[]
   */
  public $features;
  /**
   * The ID of the Project's location for default Google Cloud resources. It
   * will be one of the available [Google App Engine
   * locations](https://cloud.google.com/about/locations#region).
   *
   * @var string
   */
  public $locationId;
  /**
   * Indicates whether the location for default Google Cloud resources is a
   * [regional or multi-regional
   * location](https://firebase.google.com/docs/projects/locations#types) for
   * data replication.
   *
   * @var string
   */
  public $type;

  /**
   * Products and services that are available in the location for default Google
   * Cloud resources.
   *
   * @param string[] $features
   */
  public function setFeatures($features)
  {
    $this->features = $features;
  }
  /**
   * @return string[]
   */
  public function getFeatures()
  {
    return $this->features;
  }
  /**
   * The ID of the Project's location for default Google Cloud resources. It
   * will be one of the available [Google App Engine
   * locations](https://cloud.google.com/about/locations#region).
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * Indicates whether the location for default Google Cloud resources is a
   * [regional or multi-regional
   * location](https://firebase.google.com/docs/projects/locations#types) for
   * data replication.
   *
   * Accepted values: LOCATION_TYPE_UNSPECIFIED, REGIONAL, MULTI_REGIONAL
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Location::class, 'Google_Service_FirebaseManagement_Location');
