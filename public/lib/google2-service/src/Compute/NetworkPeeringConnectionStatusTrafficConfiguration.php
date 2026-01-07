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

namespace Google\Service\Compute;

class NetworkPeeringConnectionStatusTrafficConfiguration extends \Google\Model
{
  /**
   * This Peering will allow IPv4 traffic and routes to be exchanged.
   * Additionally if the matching peering is IPV4_IPV6, IPv6 traffic and routes
   * will be exchanged as well.
   */
  public const STACK_TYPE_IPV4_IPV6 = 'IPV4_IPV6';
  /**
   * This Peering will only allow IPv4 traffic and routes to be exchanged, even
   * if the matching peering is IPV4_IPV6.
   */
  public const STACK_TYPE_IPV4_ONLY = 'IPV4_ONLY';
  /**
   * Whether custom routes are being exported to the peer network.
   *
   * @var bool
   */
  public $exportCustomRoutesToPeer;
  /**
   * Whether subnet routes with public IP ranges are being exported to the peer
   * network.
   *
   * @var bool
   */
  public $exportSubnetRoutesWithPublicIpToPeer;
  /**
   * Whether custom routes are being imported from the peer network.
   *
   * @var bool
   */
  public $importCustomRoutesFromPeer;
  /**
   * Whether subnet routes with public IP ranges are being imported from the
   * peer network.
   *
   * @var bool
   */
  public $importSubnetRoutesWithPublicIpFromPeer;
  /**
   * Which IP version(s) of traffic and routes are being imported or exported
   * between peer networks.
   *
   * @var string
   */
  public $stackType;

  /**
   * Whether custom routes are being exported to the peer network.
   *
   * @param bool $exportCustomRoutesToPeer
   */
  public function setExportCustomRoutesToPeer($exportCustomRoutesToPeer)
  {
    $this->exportCustomRoutesToPeer = $exportCustomRoutesToPeer;
  }
  /**
   * @return bool
   */
  public function getExportCustomRoutesToPeer()
  {
    return $this->exportCustomRoutesToPeer;
  }
  /**
   * Whether subnet routes with public IP ranges are being exported to the peer
   * network.
   *
   * @param bool $exportSubnetRoutesWithPublicIpToPeer
   */
  public function setExportSubnetRoutesWithPublicIpToPeer($exportSubnetRoutesWithPublicIpToPeer)
  {
    $this->exportSubnetRoutesWithPublicIpToPeer = $exportSubnetRoutesWithPublicIpToPeer;
  }
  /**
   * @return bool
   */
  public function getExportSubnetRoutesWithPublicIpToPeer()
  {
    return $this->exportSubnetRoutesWithPublicIpToPeer;
  }
  /**
   * Whether custom routes are being imported from the peer network.
   *
   * @param bool $importCustomRoutesFromPeer
   */
  public function setImportCustomRoutesFromPeer($importCustomRoutesFromPeer)
  {
    $this->importCustomRoutesFromPeer = $importCustomRoutesFromPeer;
  }
  /**
   * @return bool
   */
  public function getImportCustomRoutesFromPeer()
  {
    return $this->importCustomRoutesFromPeer;
  }
  /**
   * Whether subnet routes with public IP ranges are being imported from the
   * peer network.
   *
   * @param bool $importSubnetRoutesWithPublicIpFromPeer
   */
  public function setImportSubnetRoutesWithPublicIpFromPeer($importSubnetRoutesWithPublicIpFromPeer)
  {
    $this->importSubnetRoutesWithPublicIpFromPeer = $importSubnetRoutesWithPublicIpFromPeer;
  }
  /**
   * @return bool
   */
  public function getImportSubnetRoutesWithPublicIpFromPeer()
  {
    return $this->importSubnetRoutesWithPublicIpFromPeer;
  }
  /**
   * Which IP version(s) of traffic and routes are being imported or exported
   * between peer networks.
   *
   * Accepted values: IPV4_IPV6, IPV4_ONLY
   *
   * @param self::STACK_TYPE_* $stackType
   */
  public function setStackType($stackType)
  {
    $this->stackType = $stackType;
  }
  /**
   * @return self::STACK_TYPE_*
   */
  public function getStackType()
  {
    return $this->stackType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkPeeringConnectionStatusTrafficConfiguration::class, 'Google_Service_Compute_NetworkPeeringConnectionStatusTrafficConfiguration');
