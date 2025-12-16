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

class ExternalVpnGatewayInterface extends \Google\Model
{
  /**
   * The numeric ID of this interface. The allowed input values for this id for
   * different redundancy types of external VPN gateway:        -
   * SINGLE_IP_INTERNALLY_REDUNDANT - 0    - TWO_IPS_REDUNDANCY - 0, 1    -
   * FOUR_IPS_REDUNDANCY - 0, 1, 2, 3
   *
   * @var string
   */
  public $id;
  /**
   * IP address of the interface in the external VPN gateway. Only IPv4 is
   * supported. This IP address can be either from your on-premise gateway or
   * another Cloud provider's VPN gateway, it cannot be an IP address from
   * Google Compute Engine.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * IPv6 address of the interface in the external VPN gateway. This IPv6
   * address can be either from your on-premise gateway or another Cloud
   * provider's VPN gateway, it cannot be an IP address from Google Compute
   * Engine. Must specify an IPv6 address (not IPV4-mapped) using any format
   * described in RFC 4291 (e.g. 2001:db8:0:0:2d9:51:0:0). The output format is
   * RFC 5952 format (e.g. 2001:db8::2d9:51:0:0).
   *
   * @var string
   */
  public $ipv6Address;

  /**
   * The numeric ID of this interface. The allowed input values for this id for
   * different redundancy types of external VPN gateway:        -
   * SINGLE_IP_INTERNALLY_REDUNDANT - 0    - TWO_IPS_REDUNDANCY - 0, 1    -
   * FOUR_IPS_REDUNDANCY - 0, 1, 2, 3
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * IP address of the interface in the external VPN gateway. Only IPv4 is
   * supported. This IP address can be either from your on-premise gateway or
   * another Cloud provider's VPN gateway, it cannot be an IP address from
   * Google Compute Engine.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * IPv6 address of the interface in the external VPN gateway. This IPv6
   * address can be either from your on-premise gateway or another Cloud
   * provider's VPN gateway, it cannot be an IP address from Google Compute
   * Engine. Must specify an IPv6 address (not IPV4-mapped) using any format
   * described in RFC 4291 (e.g. 2001:db8:0:0:2d9:51:0:0). The output format is
   * RFC 5952 format (e.g. 2001:db8::2d9:51:0:0).
   *
   * @param string $ipv6Address
   */
  public function setIpv6Address($ipv6Address)
  {
    $this->ipv6Address = $ipv6Address;
  }
  /**
   * @return string
   */
  public function getIpv6Address()
  {
    return $this->ipv6Address;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalVpnGatewayInterface::class, 'Google_Service_Compute_ExternalVpnGatewayInterface');
