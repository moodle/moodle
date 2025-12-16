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

namespace Google\Service\Networkconnectivity;

class SpokeTypeCount extends \Google\Model
{
  /**
   * Unspecified spoke type.
   */
  public const SPOKE_TYPE_SPOKE_TYPE_UNSPECIFIED = 'SPOKE_TYPE_UNSPECIFIED';
  /**
   * Spokes associated with VPN tunnels.
   */
  public const SPOKE_TYPE_VPN_TUNNEL = 'VPN_TUNNEL';
  /**
   * Spokes associated with VLAN attachments.
   */
  public const SPOKE_TYPE_INTERCONNECT_ATTACHMENT = 'INTERCONNECT_ATTACHMENT';
  /**
   * Spokes associated with router appliance instances.
   */
  public const SPOKE_TYPE_ROUTER_APPLIANCE = 'ROUTER_APPLIANCE';
  /**
   * Spokes associated with VPC networks.
   */
  public const SPOKE_TYPE_VPC_NETWORK = 'VPC_NETWORK';
  /**
   * Spokes that are backed by a producer VPC network.
   */
  public const SPOKE_TYPE_PRODUCER_VPC_NETWORK = 'PRODUCER_VPC_NETWORK';
  /**
   * Output only. The total number of spokes of this type that are associated
   * with the hub.
   *
   * @var string
   */
  public $count;
  /**
   * Output only. The type of the spokes.
   *
   * @var string
   */
  public $spokeType;

  /**
   * Output only. The total number of spokes of this type that are associated
   * with the hub.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Output only. The type of the spokes.
   *
   * Accepted values: SPOKE_TYPE_UNSPECIFIED, VPN_TUNNEL,
   * INTERCONNECT_ATTACHMENT, ROUTER_APPLIANCE, VPC_NETWORK,
   * PRODUCER_VPC_NETWORK
   *
   * @param self::SPOKE_TYPE_* $spokeType
   */
  public function setSpokeType($spokeType)
  {
    $this->spokeType = $spokeType;
  }
  /**
   * @return self::SPOKE_TYPE_*
   */
  public function getSpokeType()
  {
    return $this->spokeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpokeTypeCount::class, 'Google_Service_Networkconnectivity_SpokeTypeCount');
