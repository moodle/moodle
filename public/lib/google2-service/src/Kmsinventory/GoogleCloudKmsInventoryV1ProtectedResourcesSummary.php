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

namespace Google\Service\Kmsinventory;

class GoogleCloudKmsInventoryV1ProtectedResourcesSummary extends \Google\Model
{
  /**
   * The number of resources protected by the key grouped by Cloud product.
   *
   * @var string[]
   */
  public $cloudProducts;
  /**
   * The number of resources protected by the key grouped by region.
   *
   * @var string[]
   */
  public $locations;
  /**
   * The full name of the ProtectedResourcesSummary resource. Example:
   * projects/test-project/locations/us/keyRings/test-keyring/cryptoKeys/test-
   * key/protectedResourcesSummary
   *
   * @var string
   */
  public $name;
  /**
   * The number of distinct Cloud projects in the same Cloud organization as the
   * key that have resources protected by the key.
   *
   * @var int
   */
  public $projectCount;
  /**
   * The total number of protected resources in the same Cloud organization as
   * the key.
   *
   * @var string
   */
  public $resourceCount;
  /**
   * The number of resources protected by the key grouped by resource type.
   *
   * @var string[]
   */
  public $resourceTypes;

  /**
   * The number of resources protected by the key grouped by Cloud product.
   *
   * @param string[] $cloudProducts
   */
  public function setCloudProducts($cloudProducts)
  {
    $this->cloudProducts = $cloudProducts;
  }
  /**
   * @return string[]
   */
  public function getCloudProducts()
  {
    return $this->cloudProducts;
  }
  /**
   * The number of resources protected by the key grouped by region.
   *
   * @param string[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return string[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * The full name of the ProtectedResourcesSummary resource. Example:
   * projects/test-project/locations/us/keyRings/test-keyring/cryptoKeys/test-
   * key/protectedResourcesSummary
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
  /**
   * The number of distinct Cloud projects in the same Cloud organization as the
   * key that have resources protected by the key.
   *
   * @param int $projectCount
   */
  public function setProjectCount($projectCount)
  {
    $this->projectCount = $projectCount;
  }
  /**
   * @return int
   */
  public function getProjectCount()
  {
    return $this->projectCount;
  }
  /**
   * The total number of protected resources in the same Cloud organization as
   * the key.
   *
   * @param string $resourceCount
   */
  public function setResourceCount($resourceCount)
  {
    $this->resourceCount = $resourceCount;
  }
  /**
   * @return string
   */
  public function getResourceCount()
  {
    return $this->resourceCount;
  }
  /**
   * The number of resources protected by the key grouped by resource type.
   *
   * @param string[] $resourceTypes
   */
  public function setResourceTypes($resourceTypes)
  {
    $this->resourceTypes = $resourceTypes;
  }
  /**
   * @return string[]
   */
  public function getResourceTypes()
  {
    return $this->resourceTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudKmsInventoryV1ProtectedResourcesSummary::class, 'Google_Service_Kmsinventory_GoogleCloudKmsInventoryV1ProtectedResourcesSummary');
