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

namespace Google\Service\AccessContextManager;

class VpcSubNetwork extends \Google\Collection
{
  protected $collection_key = 'vpcIpSubnetworks';
  /**
   * Required. Network name. If the network is not part of the organization, the
   * `compute.network.get` permission must be granted to the caller. Format: `//
   * compute.googleapis.com/projects/{PROJECT_ID}/global/networks/{NETWORK_NAME}
   * ` Example: `//compute.googleapis.com/projects/my-
   * project/global/networks/network-1`
   *
   * @var string
   */
  public $network;
  /**
   * CIDR block IP subnetwork specification. The IP address must be an IPv4
   * address and can be a public or private IP address. Note that for a CIDR IP
   * address block, the specified IP address portion must be properly truncated
   * (i.e. all the host bits must be zero) or the input is considered malformed.
   * For example, "192.0.2.0/24" is accepted but "192.0.2.1/24" is not. If
   * empty, all IP addresses are allowed.
   *
   * @var string[]
   */
  public $vpcIpSubnetworks;

  /**
   * Required. Network name. If the network is not part of the organization, the
   * `compute.network.get` permission must be granted to the caller. Format: `//
   * compute.googleapis.com/projects/{PROJECT_ID}/global/networks/{NETWORK_NAME}
   * ` Example: `//compute.googleapis.com/projects/my-
   * project/global/networks/network-1`
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * CIDR block IP subnetwork specification. The IP address must be an IPv4
   * address and can be a public or private IP address. Note that for a CIDR IP
   * address block, the specified IP address portion must be properly truncated
   * (i.e. all the host bits must be zero) or the input is considered malformed.
   * For example, "192.0.2.0/24" is accepted but "192.0.2.1/24" is not. If
   * empty, all IP addresses are allowed.
   *
   * @param string[] $vpcIpSubnetworks
   */
  public function setVpcIpSubnetworks($vpcIpSubnetworks)
  {
    $this->vpcIpSubnetworks = $vpcIpSubnetworks;
  }
  /**
   * @return string[]
   */
  public function getVpcIpSubnetworks()
  {
    return $this->vpcIpSubnetworks;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpcSubNetwork::class, 'Google_Service_AccessContextManager_VpcSubNetwork');
