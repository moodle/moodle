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

namespace Google\Service\VMwareEngine;

class ManagementDnsZoneBinding extends \Google\Model
{
  /**
   * The default value. This value should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The binding is ready.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The binding is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The binding is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The binding is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The binding has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * User-provided description for this resource.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The resource name of this binding. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/managementDnsZoneBindings/my-management-dns-zone-binding`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state of the resource.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Last update time of this resource.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Network to bind is a VMware Engine network. Specify the name in the
   * following form for VMware engine network: `projects/{project}/locations/glo
   * bal/vmwareEngineNetworks/{vmware_engine_network_id}`. `{project}` can
   * either be a project number or a project ID.
   *
   * @var string
   */
  public $vmwareEngineNetwork;
  /**
   * Network to bind is a standard consumer VPC. Specify the name in the
   * following form for consumer VPC network:
   * `projects/{project}/global/networks/{network_id}`. `{project}` can either
   * be a project number or a project ID.
   *
   * @var string
   */
  public $vpcNetwork;

  /**
   * Output only. Creation time of this resource.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * User-provided description for this resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. The resource name of this binding. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/managementDnsZoneBindings/my-management-dns-zone-binding`
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
   * Output only. The state of the resource.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, UPDATING, DELETING,
   * FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. System-generated unique identifier for the resource.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Last update time of this resource.
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
  /**
   * Network to bind is a VMware Engine network. Specify the name in the
   * following form for VMware engine network: `projects/{project}/locations/glo
   * bal/vmwareEngineNetworks/{vmware_engine_network_id}`. `{project}` can
   * either be a project number or a project ID.
   *
   * @param string $vmwareEngineNetwork
   */
  public function setVmwareEngineNetwork($vmwareEngineNetwork)
  {
    $this->vmwareEngineNetwork = $vmwareEngineNetwork;
  }
  /**
   * @return string
   */
  public function getVmwareEngineNetwork()
  {
    return $this->vmwareEngineNetwork;
  }
  /**
   * Network to bind is a standard consumer VPC. Specify the name in the
   * following form for consumer VPC network:
   * `projects/{project}/global/networks/{network_id}`. `{project}` can either
   * be a project number or a project ID.
   *
   * @param string $vpcNetwork
   */
  public function setVpcNetwork($vpcNetwork)
  {
    $this->vpcNetwork = $vpcNetwork;
  }
  /**
   * @return string
   */
  public function getVpcNetwork()
  {
    return $this->vpcNetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagementDnsZoneBinding::class, 'Google_Service_VMwareEngine_ManagementDnsZoneBinding');
