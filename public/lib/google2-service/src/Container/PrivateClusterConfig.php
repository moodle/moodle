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

class PrivateClusterConfig extends \Google\Model
{
  /**
   * Whether the master's internal IP address is used as the cluster endpoint.
   * Deprecated: Use
   * ControlPlaneEndpointsConfig.IPEndpointsConfig.enable_public_endpoint
   * instead. Note that the value of enable_public_endpoint is reversed: if
   * enable_private_endpoint is false, then enable_public_endpoint will be true.
   *
   * @deprecated
   * @var bool
   */
  public $enablePrivateEndpoint;
  /**
   * Whether nodes have internal IP addresses only. If enabled, all nodes are
   * given only RFC 1918 private addresses and communicate with the master via
   * private networking. Deprecated: Use
   * NetworkConfig.default_enable_private_nodes instead.
   *
   * @deprecated
   * @var bool
   */
  public $enablePrivateNodes;
  protected $masterGlobalAccessConfigType = PrivateClusterMasterGlobalAccessConfig::class;
  protected $masterGlobalAccessConfigDataType = '';
  /**
   * The IP range in CIDR notation to use for the hosted master network. This
   * range will be used for assigning internal IP addresses to the master or set
   * of masters, as well as the ILB VIP. This range must not overlap with any
   * other ranges in use within the cluster's network.
   *
   * @var string
   */
  public $masterIpv4CidrBlock;
  /**
   * Output only. The peering name in the customer VPC used by this cluster.
   *
   * @var string
   */
  public $peeringName;
  /**
   * Output only. The internal IP address of this cluster's master endpoint.
   * Deprecated: Use
   * ControlPlaneEndpointsConfig.IPEndpointsConfig.private_endpoint instead.
   *
   * @deprecated
   * @var string
   */
  public $privateEndpoint;
  /**
   * Subnet to provision the master's private endpoint during cluster creation.
   * Specified in projects/regions/subnetworks format. Deprecated: Use
   * ControlPlaneEndpointsConfig.IPEndpointsConfig.private_endpoint_subnetwork
   * instead.
   *
   * @deprecated
   * @var string
   */
  public $privateEndpointSubnetwork;
  /**
   * Output only. The external IP address of this cluster's master endpoint.
   * Deprecated:Use
   * ControlPlaneEndpointsConfig.IPEndpointsConfig.public_endpoint instead.
   *
   * @deprecated
   * @var string
   */
  public $publicEndpoint;

  /**
   * Whether the master's internal IP address is used as the cluster endpoint.
   * Deprecated: Use
   * ControlPlaneEndpointsConfig.IPEndpointsConfig.enable_public_endpoint
   * instead. Note that the value of enable_public_endpoint is reversed: if
   * enable_private_endpoint is false, then enable_public_endpoint will be true.
   *
   * @deprecated
   * @param bool $enablePrivateEndpoint
   */
  public function setEnablePrivateEndpoint($enablePrivateEndpoint)
  {
    $this->enablePrivateEndpoint = $enablePrivateEndpoint;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnablePrivateEndpoint()
  {
    return $this->enablePrivateEndpoint;
  }
  /**
   * Whether nodes have internal IP addresses only. If enabled, all nodes are
   * given only RFC 1918 private addresses and communicate with the master via
   * private networking. Deprecated: Use
   * NetworkConfig.default_enable_private_nodes instead.
   *
   * @deprecated
   * @param bool $enablePrivateNodes
   */
  public function setEnablePrivateNodes($enablePrivateNodes)
  {
    $this->enablePrivateNodes = $enablePrivateNodes;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnablePrivateNodes()
  {
    return $this->enablePrivateNodes;
  }
  /**
   * Controls master global access settings. Deprecated: Use
   * ControlPlaneEndpointsConfig.IPEndpointsConfig.enable_global_access instead.
   *
   * @deprecated
   * @param PrivateClusterMasterGlobalAccessConfig $masterGlobalAccessConfig
   */
  public function setMasterGlobalAccessConfig(PrivateClusterMasterGlobalAccessConfig $masterGlobalAccessConfig)
  {
    $this->masterGlobalAccessConfig = $masterGlobalAccessConfig;
  }
  /**
   * @deprecated
   * @return PrivateClusterMasterGlobalAccessConfig
   */
  public function getMasterGlobalAccessConfig()
  {
    return $this->masterGlobalAccessConfig;
  }
  /**
   * The IP range in CIDR notation to use for the hosted master network. This
   * range will be used for assigning internal IP addresses to the master or set
   * of masters, as well as the ILB VIP. This range must not overlap with any
   * other ranges in use within the cluster's network.
   *
   * @param string $masterIpv4CidrBlock
   */
  public function setMasterIpv4CidrBlock($masterIpv4CidrBlock)
  {
    $this->masterIpv4CidrBlock = $masterIpv4CidrBlock;
  }
  /**
   * @return string
   */
  public function getMasterIpv4CidrBlock()
  {
    return $this->masterIpv4CidrBlock;
  }
  /**
   * Output only. The peering name in the customer VPC used by this cluster.
   *
   * @param string $peeringName
   */
  public function setPeeringName($peeringName)
  {
    $this->peeringName = $peeringName;
  }
  /**
   * @return string
   */
  public function getPeeringName()
  {
    return $this->peeringName;
  }
  /**
   * Output only. The internal IP address of this cluster's master endpoint.
   * Deprecated: Use
   * ControlPlaneEndpointsConfig.IPEndpointsConfig.private_endpoint instead.
   *
   * @deprecated
   * @param string $privateEndpoint
   */
  public function setPrivateEndpoint($privateEndpoint)
  {
    $this->privateEndpoint = $privateEndpoint;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPrivateEndpoint()
  {
    return $this->privateEndpoint;
  }
  /**
   * Subnet to provision the master's private endpoint during cluster creation.
   * Specified in projects/regions/subnetworks format. Deprecated: Use
   * ControlPlaneEndpointsConfig.IPEndpointsConfig.private_endpoint_subnetwork
   * instead.
   *
   * @deprecated
   * @param string $privateEndpointSubnetwork
   */
  public function setPrivateEndpointSubnetwork($privateEndpointSubnetwork)
  {
    $this->privateEndpointSubnetwork = $privateEndpointSubnetwork;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPrivateEndpointSubnetwork()
  {
    return $this->privateEndpointSubnetwork;
  }
  /**
   * Output only. The external IP address of this cluster's master endpoint.
   * Deprecated:Use
   * ControlPlaneEndpointsConfig.IPEndpointsConfig.public_endpoint instead.
   *
   * @deprecated
   * @param string $publicEndpoint
   */
  public function setPublicEndpoint($publicEndpoint)
  {
    $this->publicEndpoint = $publicEndpoint;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPublicEndpoint()
  {
    return $this->publicEndpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivateClusterConfig::class, 'Google_Service_Container_PrivateClusterConfig');
