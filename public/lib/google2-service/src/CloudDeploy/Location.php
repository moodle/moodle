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

namespace Google\Service\CloudDeploy;

class Location extends \Google\Model
{
  /**
   * The friendly name for this location, typically a nearby city name. For
   * example, "Tokyo".
   *
   * @var string
   */
  public $displayName;
  /**
   * Cross-service attributes for the location. For example
   * {"cloud.googleapis.com/region": "us-east1"}
   *
   * @var string[]
   */
  public $labels;
  /**
   * The canonical id for this location. For example: `"us-east1"`.
   *
   * @var string
   */
  public $locationId;
  /**
   * Service-specific metadata. For example the available capacity at the given
   * location.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * Resource name for the location, which may vary between implementations. For
   * example: `"projects/example-project/locations/us-east1"`
   *
   * @var string
   */
  public $name;

  /**
   * The friendly name for this location, typically a nearby city name. For
   * example, "Tokyo".
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
   * Cross-service attributes for the location. For example
   * {"cloud.googleapis.com/region": "us-east1"}
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The canonical id for this location. For example: `"us-east1"`.
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
   * Service-specific metadata. For example the available capacity at the given
   * location.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Resource name for the location, which may vary between implementations. For
   * example: `"projects/example-project/locations/us-east1"`
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
class_alias(Location::class, 'Google_Service_CloudDeploy_Location');
