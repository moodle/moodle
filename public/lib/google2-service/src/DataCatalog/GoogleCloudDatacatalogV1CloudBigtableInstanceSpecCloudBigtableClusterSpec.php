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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1CloudBigtableInstanceSpecCloudBigtableClusterSpec extends \Google\Model
{
  /**
   * Name of the cluster.
   *
   * @var string
   */
  public $displayName;
  /**
   * A link back to the parent resource, in this case Instance.
   *
   * @var string
   */
  public $linkedResource;
  /**
   * Location of the cluster, typically a Cloud zone.
   *
   * @var string
   */
  public $location;
  /**
   * Type of the resource. For a cluster this would be "CLUSTER".
   *
   * @var string
   */
  public $type;

  /**
   * Name of the cluster.
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
   * A link back to the parent resource, in this case Instance.
   *
   * @param string $linkedResource
   */
  public function setLinkedResource($linkedResource)
  {
    $this->linkedResource = $linkedResource;
  }
  /**
   * @return string
   */
  public function getLinkedResource()
  {
    return $this->linkedResource;
  }
  /**
   * Location of the cluster, typically a Cloud zone.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Type of the resource. For a cluster this would be "CLUSTER".
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
class_alias(GoogleCloudDatacatalogV1CloudBigtableInstanceSpecCloudBigtableClusterSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1CloudBigtableInstanceSpecCloudBigtableClusterSpec');
