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

namespace Google\Service\Networkconnectivity;

class PscConfig extends \Google\Collection
{
  /**
   * Producer instance location is not specified. When this option is chosen,
   * then the PSC connections created by this ServiceConnectionPolicy must be
   * within the same project as the Producer instance. This is the default
   * ProducerInstanceLocation value. To allow for PSC connections from this
   * network to other networks, use the CUSTOM_RESOURCE_HIERARCHY_LEVELS option.
   */
  public const PRODUCER_INSTANCE_LOCATION_PRODUCER_INSTANCE_LOCATION_UNSPECIFIED = 'PRODUCER_INSTANCE_LOCATION_UNSPECIFIED';
  /**
   * Producer instance must be within one of the values provided in
   * allowed_google_producers_resource_hierarchy_level.
   */
  public const PRODUCER_INSTANCE_LOCATION_CUSTOM_RESOURCE_HIERARCHY_LEVELS = 'CUSTOM_RESOURCE_HIERARCHY_LEVELS';
  protected $collection_key = 'subnetworks';
  /**
   * Optional. List of Projects, Folders, or Organizations from where the
   * Producer instance can be within. For example, a network administrator can
   * provide both 'organizations/foo' and 'projects/bar' as
   * allowed_google_producers_resource_hierarchy_levels. This allowlists this
   * network to connect with any Producer instance within the 'foo' organization
   * or the 'bar' project. By default,
   * allowed_google_producers_resource_hierarchy_level is empty. The format for
   * each allowed_google_producers_resource_hierarchy_level is / where is one of
   * 'projects', 'folders', or 'organizations' and is either the ID or the
   * number of the resource type. Format for each
   * allowed_google_producers_resource_hierarchy_level value: 'projects/' or
   * 'folders/' or 'organizations/' Eg. [projects/my-project-id, projects/567,
   * folders/891, organizations/123]
   *
   * @var string[]
   */
  public $allowedGoogleProducersResourceHierarchyLevel;
  /**
   * Optional. Max number of PSC connections for this policy.
   *
   * @var string
   */
  public $limit;
  /**
   * Optional. ProducerInstanceLocation is used to specify which authorization
   * mechanism to use to determine which projects the Producer instance can be
   * within.
   *
   * @var string
   */
  public $producerInstanceLocation;
  /**
   * The resource paths of subnetworks to use for IP address management.
   * Example:
   * projects/{projectNumOrId}/regions/{region}/subnetworks/{resourceId}.
   *
   * @var string[]
   */
  public $subnetworks;

  /**
   * Optional. List of Projects, Folders, or Organizations from where the
   * Producer instance can be within. For example, a network administrator can
   * provide both 'organizations/foo' and 'projects/bar' as
   * allowed_google_producers_resource_hierarchy_levels. This allowlists this
   * network to connect with any Producer instance within the 'foo' organization
   * or the 'bar' project. By default,
   * allowed_google_producers_resource_hierarchy_level is empty. The format for
   * each allowed_google_producers_resource_hierarchy_level is / where is one of
   * 'projects', 'folders', or 'organizations' and is either the ID or the
   * number of the resource type. Format for each
   * allowed_google_producers_resource_hierarchy_level value: 'projects/' or
   * 'folders/' or 'organizations/' Eg. [projects/my-project-id, projects/567,
   * folders/891, organizations/123]
   *
   * @param string[] $allowedGoogleProducersResourceHierarchyLevel
   */
  public function setAllowedGoogleProducersResourceHierarchyLevel($allowedGoogleProducersResourceHierarchyLevel)
  {
    $this->allowedGoogleProducersResourceHierarchyLevel = $allowedGoogleProducersResourceHierarchyLevel;
  }
  /**
   * @return string[]
   */
  public function getAllowedGoogleProducersResourceHierarchyLevel()
  {
    return $this->allowedGoogleProducersResourceHierarchyLevel;
  }
  /**
   * Optional. Max number of PSC connections for this policy.
   *
   * @param string $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return string
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * Optional. ProducerInstanceLocation is used to specify which authorization
   * mechanism to use to determine which projects the Producer instance can be
   * within.
   *
   * Accepted values: PRODUCER_INSTANCE_LOCATION_UNSPECIFIED,
   * CUSTOM_RESOURCE_HIERARCHY_LEVELS
   *
   * @param self::PRODUCER_INSTANCE_LOCATION_* $producerInstanceLocation
   */
  public function setProducerInstanceLocation($producerInstanceLocation)
  {
    $this->producerInstanceLocation = $producerInstanceLocation;
  }
  /**
   * @return self::PRODUCER_INSTANCE_LOCATION_*
   */
  public function getProducerInstanceLocation()
  {
    return $this->producerInstanceLocation;
  }
  /**
   * The resource paths of subnetworks to use for IP address management.
   * Example:
   * projects/{projectNumOrId}/regions/{region}/subnetworks/{resourceId}.
   *
   * @param string[] $subnetworks
   */
  public function setSubnetworks($subnetworks)
  {
    $this->subnetworks = $subnetworks;
  }
  /**
   * @return string[]
   */
  public function getSubnetworks()
  {
    return $this->subnetworks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscConfig::class, 'Google_Service_Networkconnectivity_PscConfig');
