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

namespace Google\Service\NetworkManagement;

class VpnGatewayInfo extends \Google\Model
{
  /**
   * Name of a VPN gateway.
   *
   * @var string
   */
  public $displayName;
  /**
   * IP address of the VPN gateway.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * URI of a Compute Engine network where the VPN gateway is configured.
   *
   * @var string
   */
  public $networkUri;
  /**
   * Name of a Google Cloud region where this VPN gateway is configured.
   *
   * @var string
   */
  public $region;
  /**
   * URI of a VPN gateway.
   *
   * @var string
   */
  public $uri;
  /**
   * A VPN tunnel that is associated with this VPN gateway. There may be
   * multiple VPN tunnels configured on a VPN gateway, and only the one relevant
   * to the test is displayed.
   *
   * @var string
   */
  public $vpnTunnelUri;

  /**
   * Name of a VPN gateway.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * IP address of the VPN gateway.
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
   * URI of a Compute Engine network where the VPN gateway is configured.
   *
   * @param string $networkUri
   */
  public function setNetworkUri($networkUri)
  {
    $this->networkUri = $networkUri;
  }
  /**
   * @return string
   */
  public function getNetworkUri()
  {
    return $this->networkUri;
  }
  /**
   * Name of a Google Cloud region where this VPN gateway is configured.
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
   * URI of a VPN gateway.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
  /**
   * A VPN tunnel that is associated with this VPN gateway. There may be
   * multiple VPN tunnels configured on a VPN gateway, and only the one relevant
   * to the test is displayed.
   *
   * @param string $vpnTunnelUri
   */
  public function setVpnTunnelUri($vpnTunnelUri)
  {
    $this->vpnTunnelUri = $vpnTunnelUri;
  }
  /**
   * @return string
   */
  public function getVpnTunnelUri()
  {
    return $this->vpnTunnelUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpnGatewayInfo::class, 'Google_Service_NetworkManagement_VpnGatewayInfo');
