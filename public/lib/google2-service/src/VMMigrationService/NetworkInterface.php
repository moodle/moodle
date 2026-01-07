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

namespace Google\Service\VMMigrationService;

class NetworkInterface extends \Google\Model
{
  /**
   * An unspecified network tier. Will be used as PREMIUM.
   */
  public const NETWORK_TIER_COMPUTE_ENGINE_NETWORK_TIER_UNSPECIFIED = 'COMPUTE_ENGINE_NETWORK_TIER_UNSPECIFIED';
  /**
   * A standard network tier.
   */
  public const NETWORK_TIER_NETWORK_TIER_STANDARD = 'NETWORK_TIER_STANDARD';
  /**
   * A premium network tier.
   */
  public const NETWORK_TIER_NETWORK_TIER_PREMIUM = 'NETWORK_TIER_PREMIUM';
  /**
   * Optional. The external IP to define in the NIC.
   *
   * @var string
   */
  public $externalIp;
  /**
   * Optional. The internal IP to define in the NIC. The formats accepted are:
   * `ephemeral` \ ipv4 address \ a named address resource full path.
   *
   * @var string
   */
  public $internalIp;
  /**
   * Optional. The network to connect the NIC to.
   *
   * @var string
   */
  public $network;
  /**
   * Optional. The networking tier used for optimizing connectivity between
   * instances and systems on the internet. Applies only for external ephemeral
   * IP addresses. If left empty, will default to PREMIUM.
   *
   * @var string
   */
  public $networkTier;
  /**
   * Optional. The subnetwork to connect the NIC to.
   *
   * @var string
   */
  public $subnetwork;

  /**
   * Optional. The external IP to define in the NIC.
   *
   * @param string $externalIp
   */
  public function setExternalIp($externalIp)
  {
    $this->externalIp = $externalIp;
  }
  /**
   * @return string
   */
  public function getExternalIp()
  {
    return $this->externalIp;
  }
  /**
   * Optional. The internal IP to define in the NIC. The formats accepted are:
   * `ephemeral` \ ipv4 address \ a named address resource full path.
   *
   * @param string $internalIp
   */
  public function setInternalIp($internalIp)
  {
    $this->internalIp = $internalIp;
  }
  /**
   * @return string
   */
  public function getInternalIp()
  {
    return $this->internalIp;
  }
  /**
   * Optional. The network to connect the NIC to.
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
   * Optional. The networking tier used for optimizing connectivity between
   * instances and systems on the internet. Applies only for external ephemeral
   * IP addresses. If left empty, will default to PREMIUM.
   *
   * Accepted values: COMPUTE_ENGINE_NETWORK_TIER_UNSPECIFIED,
   * NETWORK_TIER_STANDARD, NETWORK_TIER_PREMIUM
   *
   * @param self::NETWORK_TIER_* $networkTier
   */
  public function setNetworkTier($networkTier)
  {
    $this->networkTier = $networkTier;
  }
  /**
   * @return self::NETWORK_TIER_*
   */
  public function getNetworkTier()
  {
    return $this->networkTier;
  }
  /**
   * Optional. The subnetwork to connect the NIC to.
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
class_alias(NetworkInterface::class, 'Google_Service_VMMigrationService_NetworkInterface');
