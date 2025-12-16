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

class CloudResource extends \Google\Model
{
  /**
   * Unspecified resource kind.
   */
  public const KIND_RESOURCE_KIND_UNSPECIFIED = 'RESOURCE_KIND_UNSPECIFIED';
  /**
   * This is a compute instance.
   */
  public const KIND_RESOURCE_KIND_INSTANCE = 'RESOURCE_KIND_INSTANCE';
  /**
   * This is a compute disk.
   */
  public const KIND_RESOURCE_KIND_DISK = 'RESOURCE_KIND_DISK';
  /**
   * This is a compute address.
   */
  public const KIND_RESOURCE_KIND_ADDRESS = 'RESOURCE_KIND_ADDRESS';
  /**
   * This is a filestore instance.
   */
  public const KIND_RESOURCE_KIND_FILESTORE = 'RESOURCE_KIND_FILESTORE';
  /**
   * This is a compute health check.
   */
  public const KIND_RESOURCE_KIND_HEALTH_CHECK = 'RESOURCE_KIND_HEALTH_CHECK';
  /**
   * This is a compute forwarding rule.
   */
  public const KIND_RESOURCE_KIND_FORWARDING_RULE = 'RESOURCE_KIND_FORWARDING_RULE';
  /**
   * This is a compute backend service.
   */
  public const KIND_RESOURCE_KIND_BACKEND_SERVICE = 'RESOURCE_KIND_BACKEND_SERVICE';
  /**
   * This is a compute subnetwork.
   */
  public const KIND_RESOURCE_KIND_SUBNETWORK = 'RESOURCE_KIND_SUBNETWORK';
  /**
   * This is a compute network.
   */
  public const KIND_RESOURCE_KIND_NETWORK = 'RESOURCE_KIND_NETWORK';
  /**
   * This is a public accessible IP Address.
   */
  public const KIND_RESOURCE_KIND_PUBLIC_ADDRESS = 'RESOURCE_KIND_PUBLIC_ADDRESS';
  /**
   * This is a compute instance group.
   */
  public const KIND_RESOURCE_KIND_INSTANCE_GROUP = 'RESOURCE_KIND_INSTANCE_GROUP';
  protected $instancePropertiesType = InstanceProperties::class;
  protected $instancePropertiesDataType = '';
  /**
   * Output only.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. resource name Example: compute.googleapis.com/projects/wlm-
   * obs-dev/zones/us-central1-a/instances/sap-pri
   *
   * @var string
   */
  public $name;

  /**
   * Output only. All instance properties.
   *
   * @param InstanceProperties $instanceProperties
   */
  public function setInstanceProperties(InstanceProperties $instanceProperties)
  {
    $this->instanceProperties = $instanceProperties;
  }
  /**
   * @return InstanceProperties
   */
  public function getInstanceProperties()
  {
    return $this->instanceProperties;
  }
  /**
   * Output only.
   *
   * Accepted values: RESOURCE_KIND_UNSPECIFIED, RESOURCE_KIND_INSTANCE,
   * RESOURCE_KIND_DISK, RESOURCE_KIND_ADDRESS, RESOURCE_KIND_FILESTORE,
   * RESOURCE_KIND_HEALTH_CHECK, RESOURCE_KIND_FORWARDING_RULE,
   * RESOURCE_KIND_BACKEND_SERVICE, RESOURCE_KIND_SUBNETWORK,
   * RESOURCE_KIND_NETWORK, RESOURCE_KIND_PUBLIC_ADDRESS,
   * RESOURCE_KIND_INSTANCE_GROUP
   *
   * @param self::KIND_* $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return self::KIND_*
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. resource name Example: compute.googleapis.com/projects/wlm-
   * obs-dev/zones/us-central1-a/instances/sap-pri
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
class_alias(CloudResource::class, 'Google_Service_WorkloadManager_CloudResource');
