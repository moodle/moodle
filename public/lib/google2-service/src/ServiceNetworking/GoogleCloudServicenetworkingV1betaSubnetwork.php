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

namespace Google\Service\ServiceNetworking;

class GoogleCloudServicenetworkingV1betaSubnetwork extends \Google\Model
{
  /**
   * Subnetwork CIDR range in `10.x.x.x/y` format.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * Subnetwork name. See https://cloud.google.com/compute/docs/vpc/
   *
   * @var string
   */
  public $name;
  /**
   * In the Shared VPC host project, the VPC network that's peered with the
   * consumer network. For example: `projects/1234321/global/networks/host-
   * network`
   *
   * @var string
   */
  public $network;
  /**
   * This is a discovered subnet that is not within the current consumer
   * allocated ranges.
   *
   * @var bool
   */
  public $outsideAllocation;

  /**
   * Subnetwork CIDR range in `10.x.x.x/y` format.
   *
   * @param string $ipCidrRange
   */
  public function setIpCidrRange($ipCidrRange)
  {
    $this->ipCidrRange = $ipCidrRange;
  }
  /**
   * @return string
   */
  public function getIpCidrRange()
  {
    return $this->ipCidrRange;
  }
  /**
   * Subnetwork name. See https://cloud.google.com/compute/docs/vpc/
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
   * In the Shared VPC host project, the VPC network that's peered with the
   * consumer network. For example: `projects/1234321/global/networks/host-
   * network`
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
   * This is a discovered subnet that is not within the current consumer
   * allocated ranges.
   *
   * @param bool $outsideAllocation
   */
  public function setOutsideAllocation($outsideAllocation)
  {
    $this->outsideAllocation = $outsideAllocation;
  }
  /**
   * @return bool
   */
  public function getOutsideAllocation()
  {
    return $this->outsideAllocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudServicenetworkingV1betaSubnetwork::class, 'Google_Service_ServiceNetworking_GoogleCloudServicenetworkingV1betaSubnetwork');
