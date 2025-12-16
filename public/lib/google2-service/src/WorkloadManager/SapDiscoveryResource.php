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

namespace Google\Service\WorkloadManager;

class SapDiscoveryResource extends \Google\Collection
{
  /**
   * Unspecified resource kind.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_UNSPECIFIED = 'RESOURCE_KIND_UNSPECIFIED';
  /**
   * This is a compute instance.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_INSTANCE = 'RESOURCE_KIND_INSTANCE';
  /**
   * This is a compute disk.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_DISK = 'RESOURCE_KIND_DISK';
  /**
   * This is a compute address.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_ADDRESS = 'RESOURCE_KIND_ADDRESS';
  /**
   * This is a filestore instance.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_FILESTORE = 'RESOURCE_KIND_FILESTORE';
  /**
   * This is a compute health check.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_HEALTH_CHECK = 'RESOURCE_KIND_HEALTH_CHECK';
  /**
   * This is a compute forwarding rule.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_FORWARDING_RULE = 'RESOURCE_KIND_FORWARDING_RULE';
  /**
   * This is a compute backend service.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_BACKEND_SERVICE = 'RESOURCE_KIND_BACKEND_SERVICE';
  /**
   * This is a compute subnetwork.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_SUBNETWORK = 'RESOURCE_KIND_SUBNETWORK';
  /**
   * This is a compute network.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_NETWORK = 'RESOURCE_KIND_NETWORK';
  /**
   * This is a public accessible IP Address.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_PUBLIC_ADDRESS = 'RESOURCE_KIND_PUBLIC_ADDRESS';
  /**
   * This is a compute instance group.
   */
  public const RESOURCE_KIND_RESOURCE_KIND_INSTANCE_GROUP = 'RESOURCE_KIND_INSTANCE_GROUP';
  /**
   * Undefined resource type.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_UNSPECIFIED = 'RESOURCE_TYPE_UNSPECIFIED';
  /**
   * This is a compute resource.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_COMPUTE = 'RESOURCE_TYPE_COMPUTE';
  /**
   * This a storage resource.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_STORAGE = 'RESOURCE_TYPE_STORAGE';
  /**
   * This is a network resource.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_NETWORK = 'RESOURCE_TYPE_NETWORK';
  protected $collection_key = 'relatedResources';
  protected $instancePropertiesType = SapDiscoveryResourceInstanceProperties::class;
  protected $instancePropertiesDataType = '';
  /**
   * Optional. A list of resource URIs related to this resource.
   *
   * @var string[]
   */
  public $relatedResources;
  /**
   * Required. ComputeInstance, ComputeDisk, VPC, Bare Metal server, etc.
   *
   * @var string
   */
  public $resourceKind;
  /**
   * Required. The type of this resource.
   *
   * @var string
   */
  public $resourceType;
  /**
   * Required. URI of the resource, includes project, location, and name.
   *
   * @var string
   */
  public $resourceUri;
  /**
   * Required. Unix timestamp of when this resource last had its discovery data
   * updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. A set of properties only applying to instance type resources.
   *
   * @param SapDiscoveryResourceInstanceProperties $instanceProperties
   */
  public function setInstanceProperties(SapDiscoveryResourceInstanceProperties $instanceProperties)
  {
    $this->instanceProperties = $instanceProperties;
  }
  /**
   * @return SapDiscoveryResourceInstanceProperties
   */
  public function getInstanceProperties()
  {
    return $this->instanceProperties;
  }
  /**
   * Optional. A list of resource URIs related to this resource.
   *
   * @param string[] $relatedResources
   */
  public function setRelatedResources($relatedResources)
  {
    $this->relatedResources = $relatedResources;
  }
  /**
   * @return string[]
   */
  public function getRelatedResources()
  {
    return $this->relatedResources;
  }
  /**
   * Required. ComputeInstance, ComputeDisk, VPC, Bare Metal server, etc.
   *
   * Accepted values: RESOURCE_KIND_UNSPECIFIED, RESOURCE_KIND_INSTANCE,
   * RESOURCE_KIND_DISK, RESOURCE_KIND_ADDRESS, RESOURCE_KIND_FILESTORE,
   * RESOURCE_KIND_HEALTH_CHECK, RESOURCE_KIND_FORWARDING_RULE,
   * RESOURCE_KIND_BACKEND_SERVICE, RESOURCE_KIND_SUBNETWORK,
   * RESOURCE_KIND_NETWORK, RESOURCE_KIND_PUBLIC_ADDRESS,
   * RESOURCE_KIND_INSTANCE_GROUP
   *
   * @param self::RESOURCE_KIND_* $resourceKind
   */
  public function setResourceKind($resourceKind)
  {
    $this->resourceKind = $resourceKind;
  }
  /**
   * @return self::RESOURCE_KIND_*
   */
  public function getResourceKind()
  {
    return $this->resourceKind;
  }
  /**
   * Required. The type of this resource.
   *
   * Accepted values: RESOURCE_TYPE_UNSPECIFIED, RESOURCE_TYPE_COMPUTE,
   * RESOURCE_TYPE_STORAGE, RESOURCE_TYPE_NETWORK
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * Required. URI of the resource, includes project, location, and name.
   *
   * @param string $resourceUri
   */
  public function setResourceUri($resourceUri)
  {
    $this->resourceUri = $resourceUri;
  }
  /**
   * @return string
   */
  public function getResourceUri()
  {
    return $this->resourceUri;
  }
  /**
   * Required. Unix timestamp of when this resource last had its discovery data
   * updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapDiscoveryResource::class, 'Google_Service_WorkloadManager_SapDiscoveryResource');
