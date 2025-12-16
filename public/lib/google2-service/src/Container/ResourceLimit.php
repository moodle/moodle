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

namespace Google\Service\Container;

class ResourceLimit extends \Google\Model
{
  /**
   * Maximum amount of the resource in the cluster.
   *
   * @var string
   */
  public $maximum;
  /**
   * Minimum amount of the resource in the cluster.
   *
   * @var string
   */
  public $minimum;
  /**
   * Resource name "cpu", "memory" or gpu-specific string.
   *
   * @var string
   */
  public $resourceType;

  /**
   * Maximum amount of the resource in the cluster.
   *
   * @param string $maximum
   */
  public function setMaximum($maximum)
  {
    $this->maximum = $maximum;
  }
  /**
   * @return string
   */
  public function getMaximum()
  {
    return $this->maximum;
  }
  /**
   * Minimum amount of the resource in the cluster.
   *
   * @param string $minimum
   */
  public function setMinimum($minimum)
  {
    $this->minimum = $minimum;
  }
  /**
   * @return string
   */
  public function getMinimum()
  {
    return $this->minimum;
  }
  /**
   * Resource name "cpu", "memory" or gpu-specific string.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceLimit::class, 'Google_Service_Container_ResourceLimit');
