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

namespace Google\Service\GKEOnPrem;

class VmwareAdminNetworkConfig extends \Google\Collection
{
  protected $collection_key = 'serviceAddressCidrBlocks';
  protected $dhcpIpConfigType = VmwareDhcpIpConfig::class;
  protected $dhcpIpConfigDataType = '';
  protected $haControlPlaneConfigType = VmwareAdminHAControlPlaneConfig::class;
  protected $haControlPlaneConfigDataType = '';
  protected $hostConfigType = VmwareHostConfig::class;
  protected $hostConfigDataType = '';
  /**
   * Required. All pods in the cluster are assigned an RFC1918 IPv4 address from
   * these ranges. Only a single range is supported. This field cannot be
   * changed after creation.
   *
   * @var string[]
   */
  public $podAddressCidrBlocks;
  /**
   * Required. All services in the cluster are assigned an RFC1918 IPv4 address
   * from these ranges. Only a single range is supported. This field cannot be
   * changed after creation.
   *
   * @var string[]
   */
  public $serviceAddressCidrBlocks;
  protected $staticIpConfigType = VmwareStaticIpConfig::class;
  protected $staticIpConfigDataType = '';
  /**
   * vcenter_network specifies vCenter network name.
   *
   * @var string
   */
  public $vcenterNetwork;

  /**
   * Configuration settings for a DHCP IP configuration.
   *
   * @param VmwareDhcpIpConfig $dhcpIpConfig
   */
  public function setDhcpIpConfig(VmwareDhcpIpConfig $dhcpIpConfig)
  {
    $this->dhcpIpConfig = $dhcpIpConfig;
  }
  /**
   * @return VmwareDhcpIpConfig
   */
  public function getDhcpIpConfig()
  {
    return $this->dhcpIpConfig;
  }
  /**
   * Configuration for HA admin cluster control plane.
   *
   * @param VmwareAdminHAControlPlaneConfig $haControlPlaneConfig
   */
  public function setHaControlPlaneConfig(VmwareAdminHAControlPlaneConfig $haControlPlaneConfig)
  {
    $this->haControlPlaneConfig = $haControlPlaneConfig;
  }
  /**
   * @return VmwareAdminHAControlPlaneConfig
   */
  public function getHaControlPlaneConfig()
  {
    return $this->haControlPlaneConfig;
  }
  /**
   * Represents common network settings irrespective of the host's IP address.
   *
   * @param VmwareHostConfig $hostConfig
   */
  public function setHostConfig(VmwareHostConfig $hostConfig)
  {
    $this->hostConfig = $hostConfig;
  }
  /**
   * @return VmwareHostConfig
   */
  public function getHostConfig()
  {
    return $this->hostConfig;
  }
  /**
   * Required. All pods in the cluster are assigned an RFC1918 IPv4 address from
   * these ranges. Only a single range is supported. This field cannot be
   * changed after creation.
   *
   * @param string[] $podAddressCidrBlocks
   */
  public function setPodAddressCidrBlocks($podAddressCidrBlocks)
  {
    $this->podAddressCidrBlocks = $podAddressCidrBlocks;
  }
  /**
   * @return string[]
   */
  public function getPodAddressCidrBlocks()
  {
    return $this->podAddressCidrBlocks;
  }
  /**
   * Required. All services in the cluster are assigned an RFC1918 IPv4 address
   * from these ranges. Only a single range is supported. This field cannot be
   * changed after creation.
   *
   * @param string[] $serviceAddressCidrBlocks
   */
  public function setServiceAddressCidrBlocks($serviceAddressCidrBlocks)
  {
    $this->serviceAddressCidrBlocks = $serviceAddressCidrBlocks;
  }
  /**
   * @return string[]
   */
  public function getServiceAddressCidrBlocks()
  {
    return $this->serviceAddressCidrBlocks;
  }
  /**
   * Configuration settings for a static IP configuration.
   *
   * @param VmwareStaticIpConfig $staticIpConfig
   */
  public function setStaticIpConfig(VmwareStaticIpConfig $staticIpConfig)
  {
    $this->staticIpConfig = $staticIpConfig;
  }
  /**
   * @return VmwareStaticIpConfig
   */
  public function getStaticIpConfig()
  {
    return $this->staticIpConfig;
  }
  /**
   * vcenter_network specifies vCenter network name.
   *
   * @param string $vcenterNetwork
   */
  public function setVcenterNetwork($vcenterNetwork)
  {
    $this->vcenterNetwork = $vcenterNetwork;
  }
  /**
   * @return string
   */
  public function getVcenterNetwork()
  {
    return $this->vcenterNetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareAdminNetworkConfig::class, 'Google_Service_GKEOnPrem_VmwareAdminNetworkConfig');
