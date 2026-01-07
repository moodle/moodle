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

namespace Google\Service\SecurityCommandCenter;

class ValuedResource extends \Google\Collection
{
  /**
   * The resource value isn't specified.
   */
  public const RESOURCE_VALUE_RESOURCE_VALUE_UNSPECIFIED = 'RESOURCE_VALUE_UNSPECIFIED';
  /**
   * This is a low-value resource.
   */
  public const RESOURCE_VALUE_RESOURCE_VALUE_LOW = 'RESOURCE_VALUE_LOW';
  /**
   * This is a medium-value resource.
   */
  public const RESOURCE_VALUE_RESOURCE_VALUE_MEDIUM = 'RESOURCE_VALUE_MEDIUM';
  /**
   * This is a high-value resource.
   */
  public const RESOURCE_VALUE_RESOURCE_VALUE_HIGH = 'RESOURCE_VALUE_HIGH';
  protected $collection_key = 'resourceValueConfigsUsed';
  /**
   * Human-readable name of the valued resource.
   *
   * @var string
   */
  public $displayName;
  /**
   * Exposed score for this valued resource. A value of 0 means no exposure was
   * detected exposure.
   *
   * @var 
   */
  public $exposedScore;
  /**
   * Valued resource name, for example, e.g.:
   * `organizations/123/simulations/456/valuedResources/789`
   *
   * @var string
   */
  public $name;
  /**
   * The [full resource name](https://cloud.google.com/apis/design/resource_name
   * s#full_resource_name) of the valued resource.
   *
   * @var string
   */
  public $resource;
  /**
   * The [resource type](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types) of the valued resource.
   *
   * @var string
   */
  public $resourceType;
  /**
   * How valuable this resource is.
   *
   * @var string
   */
  public $resourceValue;
  protected $resourceValueConfigsUsedType = ResourceValueConfigMetadata::class;
  protected $resourceValueConfigsUsedDataType = 'array';

  /**
   * Human-readable name of the valued resource.
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
  public function setExposedScore($exposedScore)
  {
    $this->exposedScore = $exposedScore;
  }
  public function getExposedScore()
  {
    return $this->exposedScore;
  }
  /**
   * Valued resource name, for example, e.g.:
   * `organizations/123/simulations/456/valuedResources/789`
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
   * The [full resource name](https://cloud.google.com/apis/design/resource_name
   * s#full_resource_name) of the valued resource.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The [resource type](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types) of the valued resource.
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
   * How valuable this resource is.
   *
   * Accepted values: RESOURCE_VALUE_UNSPECIFIED, RESOURCE_VALUE_LOW,
   * RESOURCE_VALUE_MEDIUM, RESOURCE_VALUE_HIGH
   *
   * @param self::RESOURCE_VALUE_* $resourceValue
   */
  public function setResourceValue($resourceValue)
  {
    $this->resourceValue = $resourceValue;
  }
  /**
   * @return self::RESOURCE_VALUE_*
   */
  public function getResourceValue()
  {
    return $this->resourceValue;
  }
  /**
   * List of resource value configurations' metadata used to determine the value
   * of this resource. Maximum of 100.
   *
   * @param ResourceValueConfigMetadata[] $resourceValueConfigsUsed
   */
  public function setResourceValueConfigsUsed($resourceValueConfigsUsed)
  {
    $this->resourceValueConfigsUsed = $resourceValueConfigsUsed;
  }
  /**
   * @return ResourceValueConfigMetadata[]
   */
  public function getResourceValueConfigsUsed()
  {
    return $this->resourceValueConfigsUsed;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValuedResource::class, 'Google_Service_SecurityCommandCenter_ValuedResource');
