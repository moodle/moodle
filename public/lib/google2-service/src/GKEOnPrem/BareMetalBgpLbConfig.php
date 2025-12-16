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

class BareMetalBgpLbConfig extends \Google\Collection
{
  protected $collection_key = 'bgpPeerConfigs';
  protected $addressPoolsType = BareMetalLoadBalancerAddressPool::class;
  protected $addressPoolsDataType = 'array';
  /**
   * Required. BGP autonomous system number (ASN) of the cluster. This field can
   * be updated after cluster creation.
   *
   * @var string
   */
  public $asn;
  protected $bgpPeerConfigsType = BareMetalBgpPeerConfig::class;
  protected $bgpPeerConfigsDataType = 'array';
  protected $loadBalancerNodePoolConfigType = BareMetalLoadBalancerNodePoolConfig::class;
  protected $loadBalancerNodePoolConfigDataType = '';

  /**
   * Required. AddressPools is a list of non-overlapping IP pools used by load
   * balancer typed services. All addresses must be routable to load balancer
   * nodes. IngressVIP must be included in the pools.
   *
   * @param BareMetalLoadBalancerAddressPool[] $addressPools
   */
  public function setAddressPools($addressPools)
  {
    $this->addressPools = $addressPools;
  }
  /**
   * @return BareMetalLoadBalancerAddressPool[]
   */
  public function getAddressPools()
  {
    return $this->addressPools;
  }
  /**
   * Required. BGP autonomous system number (ASN) of the cluster. This field can
   * be updated after cluster creation.
   *
   * @param string $asn
   */
  public function setAsn($asn)
  {
    $this->asn = $asn;
  }
  /**
   * @return string
   */
  public function getAsn()
  {
    return $this->asn;
  }
  /**
   * Required. The list of BGP peers that the cluster will connect to. At least
   * one peer must be configured for each control plane node. Control plane
   * nodes will connect to these peers to advertise the control plane VIP. The
   * Services load balancer also uses these peers by default. This field can be
   * updated after cluster creation.
   *
   * @param BareMetalBgpPeerConfig[] $bgpPeerConfigs
   */
  public function setBgpPeerConfigs($bgpPeerConfigs)
  {
    $this->bgpPeerConfigs = $bgpPeerConfigs;
  }
  /**
   * @return BareMetalBgpPeerConfig[]
   */
  public function getBgpPeerConfigs()
  {
    return $this->bgpPeerConfigs;
  }
  /**
   * Specifies the node pool running data plane load balancing. L2 connectivity
   * is required among nodes in this pool. If missing, the control plane node
   * pool is used for data plane load balancing.
   *
   * @param BareMetalLoadBalancerNodePoolConfig $loadBalancerNodePoolConfig
   */
  public function setLoadBalancerNodePoolConfig(BareMetalLoadBalancerNodePoolConfig $loadBalancerNodePoolConfig)
  {
    $this->loadBalancerNodePoolConfig = $loadBalancerNodePoolConfig;
  }
  /**
   * @return BareMetalLoadBalancerNodePoolConfig
   */
  public function getLoadBalancerNodePoolConfig()
  {
    return $this->loadBalancerNodePoolConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalBgpLbConfig::class, 'Google_Service_GKEOnPrem_BareMetalBgpLbConfig');
