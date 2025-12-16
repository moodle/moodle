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

class UsableSubnetwork extends \Google\Collection
{
  protected $collection_key = 'secondaryIpRanges';
  /**
   * The range of internal addresses that are owned by this subnetwork.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * Network Name. Example: projects/my-project/global/networks/my-network
   *
   * @var string
   */
  public $network;
  protected $secondaryIpRangesType = UsableSubnetworkSecondaryRange::class;
  protected $secondaryIpRangesDataType = 'array';
  /**
   * A human readable status message representing the reasons for cases where
   * the caller cannot use the secondary ranges under the subnet. For example if
   * the secondary_ip_ranges is empty due to a permission issue, an insufficient
   * permission message will be given by status_message.
   *
   * @var string
   */
  public $statusMessage;
  /**
   * Subnetwork Name. Example: projects/my-project/regions/us-
   * central1/subnetworks/my-subnet
   *
   * @var string
   */
  public $subnetwork;

  /**
   * The range of internal addresses that are owned by this subnetwork.
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
   * Network Name. Example: projects/my-project/global/networks/my-network
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
   * Secondary IP ranges.
   *
   * @param UsableSubnetworkSecondaryRange[] $secondaryIpRanges
   */
  public function setSecondaryIpRanges($secondaryIpRanges)
  {
    $this->secondaryIpRanges = $secondaryIpRanges;
  }
  /**
   * @return UsableSubnetworkSecondaryRange[]
   */
  public function getSecondaryIpRanges()
  {
    return $this->secondaryIpRanges;
  }
  /**
   * A human readable status message representing the reasons for cases where
   * the caller cannot use the secondary ranges under the subnet. For example if
   * the secondary_ip_ranges is empty due to a permission issue, an insufficient
   * permission message will be given by status_message.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * Subnetwork Name. Example: projects/my-project/regions/us-
   * central1/subnetworks/my-subnet
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsableSubnetwork::class, 'Google_Service_Container_UsableSubnetwork');
