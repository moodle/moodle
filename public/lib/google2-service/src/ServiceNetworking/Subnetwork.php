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

class Subnetwork extends \Google\Collection
{
  protected $collection_key = 'secondaryIpRanges';
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
   * GCP region where the subnetwork is located.
   *
   * @var string
   */
  public $region;
  protected $secondaryIpRangesType = SecondaryIpRange::class;
  protected $secondaryIpRangesDataType = 'array';

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
  /**
   * GCP region where the subnetwork is located.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * List of secondary IP ranges in this subnetwork.
   *
   * @param SecondaryIpRange[] $secondaryIpRanges
   */
  public function setSecondaryIpRanges($secondaryIpRanges)
  {
    $this->secondaryIpRanges = $secondaryIpRanges;
  }
  /**
   * @return SecondaryIpRange[]
   */
  public function getSecondaryIpRanges()
  {
    return $this->secondaryIpRanges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Subnetwork::class, 'Google_Service_ServiceNetworking_Subnetwork');
