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

class NetworkPolicy extends \Google\Model
{
  /**
   * Output only. Creation time of this resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User-provided description for this network policy.
   *
   * @var string
   */
  public $description;
  /**
   * Required. IP address range in CIDR notation used to create internet access
   * and external IP access. An RFC 1918 CIDR block, with a "/26" prefix, is
   * required. The range cannot overlap with any prefixes either in the consumer
   * VPC network or in use by the private clouds attached to that VPC network.
   *
   * @var string
   */
  public $edgeServicesCidr;
  protected $externalIpType = NetworkService::class;
  protected $externalIpDataType = '';
  protected $internetAccessType = NetworkService::class;
  protected $internetAccessDataType = '';
  /**
   * Output only. Identifier. The resource name of this network policy. Resource
   * names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/networkPolicies/my-network-
   * policy`
   *
   * @var string
   */
  public $name;
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
   * Optional. The relative resource name of the VMware Engine network. Specify
   * the name in the following form: `projects/{project}/locations/{location}/vm
   * wareEngineNetworks/{vmware_engine_network_id}` where `{project}` can either
   * be a project number or a project ID.
   *
   * @var string
   */
  public $vmwareEngineNetwork;
  /**
   * Output only. The canonical name of the VMware Engine network in the form: `
   * projects/{project_number}/locations/{location}/vmwareEngineNetworks/{vmware
   * _engine_network_id}`
   *
   * @var string
   */
  public $vmwareEngineNetworkCanonical;

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
   * Optional. User-provided description for this network policy.
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
   * Required. IP address range in CIDR notation used to create internet access
   * and external IP access. An RFC 1918 CIDR block, with a "/26" prefix, is
   * required. The range cannot overlap with any prefixes either in the consumer
   * VPC network or in use by the private clouds attached to that VPC network.
   *
   * @param string $edgeServicesCidr
   */
  public function setEdgeServicesCidr($edgeServicesCidr)
  {
    $this->edgeServicesCidr = $edgeServicesCidr;
  }
  /**
   * @return string
   */
  public function getEdgeServicesCidr()
  {
    return $this->edgeServicesCidr;
  }
  /**
   * Network service that allows External IP addresses to be assigned to VMware
   * workloads. This service can only be enabled when `internet_access` is also
   * enabled.
   *
   * @param NetworkService $externalIp
   */
  public function setExternalIp(NetworkService $externalIp)
  {
    $this->externalIp = $externalIp;
  }
  /**
   * @return NetworkService
   */
  public function getExternalIp()
  {
    return $this->externalIp;
  }
  /**
   * Network service that allows VMware workloads to access the internet.
   *
   * @param NetworkService $internetAccess
   */
  public function setInternetAccess(NetworkService $internetAccess)
  {
    $this->internetAccess = $internetAccess;
  }
  /**
   * @return NetworkService
   */
  public function getInternetAccess()
  {
    return $this->internetAccess;
  }
  /**
   * Output only. Identifier. The resource name of this network policy. Resource
   * names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/networkPolicies/my-network-
   * policy`
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
   * Optional. The relative resource name of the VMware Engine network. Specify
   * the name in the following form: `projects/{project}/locations/{location}/vm
   * wareEngineNetworks/{vmware_engine_network_id}` where `{project}` can either
   * be a project number or a project ID.
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
   * Output only. The canonical name of the VMware Engine network in the form: `
   * projects/{project_number}/locations/{location}/vmwareEngineNetworks/{vmware
   * _engine_network_id}`
   *
   * @param string $vmwareEngineNetworkCanonical
   */
  public function setVmwareEngineNetworkCanonical($vmwareEngineNetworkCanonical)
  {
    $this->vmwareEngineNetworkCanonical = $vmwareEngineNetworkCanonical;
  }
  /**
   * @return string
   */
  public function getVmwareEngineNetworkCanonical()
  {
    return $this->vmwareEngineNetworkCanonical;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkPolicy::class, 'Google_Service_VMwareEngine_NetworkPolicy');
