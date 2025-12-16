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

class VpnGatewayVpnGatewayInterface extends \Google\Model
{
  /**
   * Output only. [Output Only] Numeric identifier for this VPN interface
   * associated with the VPN gateway.
   *
   * @var string
   */
  public $id;
  /**
   * URL of the VLAN attachment (interconnectAttachment) resource for this VPN
   * gateway interface. When the value of this field is present, the VPN gateway
   * is used for HA VPN over Cloud Interconnect; all egress or ingress traffic
   * for this VPN gateway interface goes through the specified VLAN attachment
   * resource.
   *
   * @var string
   */
  public $interconnectAttachment;
  /**
   * Output only. [Output Only] IP address for this VPN interface associated
   * with the VPN gateway. The IP address could be either a regional external IP
   * address or a regional internal IP address. The two IP addresses for a VPN
   * gateway must be all regional external or regional internal IP addresses.
   * There cannot be a mix of regional external IP addresses and regional
   * internal IP addresses. For HA VPN over Cloud Interconnect, the IP addresses
   * for both interfaces could either be regional internal IP addresses or
   * regional external IP addresses. For regular (non HA VPN over Cloud
   * Interconnect) HA VPN tunnels, the IP address must be a regional external IP
   * address.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Output only. [Output Only] IPv6 address for this VPN interface associated
   * with the VPN gateway. The IPv6 address must be a regional external IPv6
   * address. The format is RFC 5952 format (e.g. 2001:db8::2d9:51:0:0).
   *
   * @var string
   */
  public $ipv6Address;

  /**
   * Output only. [Output Only] Numeric identifier for this VPN interface
   * associated with the VPN gateway.
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
   * URL of the VLAN attachment (interconnectAttachment) resource for this VPN
   * gateway interface. When the value of this field is present, the VPN gateway
   * is used for HA VPN over Cloud Interconnect; all egress or ingress traffic
   * for this VPN gateway interface goes through the specified VLAN attachment
   * resource.
   *
   * @param string $interconnectAttachment
   */
  public function setInterconnectAttachment($interconnectAttachment)
  {
    $this->interconnectAttachment = $interconnectAttachment;
  }
  /**
   * @return string
   */
  public function getInterconnectAttachment()
  {
    return $this->interconnectAttachment;
  }
  /**
   * Output only. [Output Only] IP address for this VPN interface associated
   * with the VPN gateway. The IP address could be either a regional external IP
   * address or a regional internal IP address. The two IP addresses for a VPN
   * gateway must be all regional external or regional internal IP addresses.
   * There cannot be a mix of regional external IP addresses and regional
   * internal IP addresses. For HA VPN over Cloud Interconnect, the IP addresses
   * for both interfaces could either be regional internal IP addresses or
   * regional external IP addresses. For regular (non HA VPN over Cloud
   * Interconnect) HA VPN tunnels, the IP address must be a regional external IP
   * address.
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
   * Output only. [Output Only] IPv6 address for this VPN interface associated
   * with the VPN gateway. The IPv6 address must be a regional external IPv6
   * address. The format is RFC 5952 format (e.g. 2001:db8::2d9:51:0:0).
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
class_alias(VpnGatewayVpnGatewayInterface::class, 'Google_Service_Compute_VpnGatewayVpnGatewayInterface');
