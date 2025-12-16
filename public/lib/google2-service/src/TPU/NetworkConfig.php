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

namespace Google\Service\TPU;

class NetworkConfig extends \Google\Model
{
  /**
   * Allows the TPU node to send and receive packets with non-matching
   * destination or source IPs. This is required if you plan to use the TPU
   * workers to forward routes.
   *
   * @var bool
   */
  public $canIpForward;
  /**
   * Indicates that external IP addresses would be associated with the TPU
   * workers. If set to false, the specified subnetwork or network should have
   * Private Google Access enabled.
   *
   * @var bool
   */
  public $enableExternalIps;
  /**
   * The name of the network for the TPU node. It must be a preexisting Google
   * Compute Engine network. If none is provided, "default" will be used.
   *
   * @var string
   */
  public $network;
  /**
   * Optional. Specifies networking queue count for TPU VM instance's network
   * interface.
   *
   * @var int
   */
  public $queueCount;
  /**
   * The name of the subnetwork for the TPU node. It must be a preexisting
   * Google Compute Engine subnetwork. If none is provided, "default" will be
   * used.
   *
   * @var string
   */
  public $subnetwork;

  /**
   * Allows the TPU node to send and receive packets with non-matching
   * destination or source IPs. This is required if you plan to use the TPU
   * workers to forward routes.
   *
   * @param bool $canIpForward
   */
  public function setCanIpForward($canIpForward)
  {
    $this->canIpForward = $canIpForward;
  }
  /**
   * @return bool
   */
  public function getCanIpForward()
  {
    return $this->canIpForward;
  }
  /**
   * Indicates that external IP addresses would be associated with the TPU
   * workers. If set to false, the specified subnetwork or network should have
   * Private Google Access enabled.
   *
   * @param bool $enableExternalIps
   */
  public function setEnableExternalIps($enableExternalIps)
  {
    $this->enableExternalIps = $enableExternalIps;
  }
  /**
   * @return bool
   */
  public function getEnableExternalIps()
  {
    return $this->enableExternalIps;
  }
  /**
   * The name of the network for the TPU node. It must be a preexisting Google
   * Compute Engine network. If none is provided, "default" will be used.
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
   * Optional. Specifies networking queue count for TPU VM instance's network
   * interface.
   *
   * @param int $queueCount
   */
  public function setQueueCount($queueCount)
  {
    $this->queueCount = $queueCount;
  }
  /**
   * @return int
   */
  public function getQueueCount()
  {
    return $this->queueCount;
  }
  /**
   * The name of the subnetwork for the TPU node. It must be a preexisting
   * Google Compute Engine subnetwork. If none is provided, "default" will be
   * used.
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
class_alias(NetworkConfig::class, 'Google_Service_TPU_NetworkConfig');
