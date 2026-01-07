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

class VpnTunnelInfo extends \Google\Model
{
  /**
   * Unspecified type. Default value.
   */
  public const ROUTING_TYPE_ROUTING_TYPE_UNSPECIFIED = 'ROUTING_TYPE_UNSPECIFIED';
  /**
   * Route based VPN.
   */
  public const ROUTING_TYPE_ROUTE_BASED = 'ROUTE_BASED';
  /**
   * Policy based routing.
   */
  public const ROUTING_TYPE_POLICY_BASED = 'POLICY_BASED';
  /**
   * Dynamic (BGP) routing.
   */
  public const ROUTING_TYPE_DYNAMIC = 'DYNAMIC';
  /**
   * Name of a VPN tunnel.
   *
   * @var string
   */
  public $displayName;
  /**
   * URI of a Compute Engine network where the VPN tunnel is configured.
   *
   * @var string
   */
  public $networkUri;
  /**
   * Name of a Google Cloud region where this VPN tunnel is configured.
   *
   * @var string
   */
  public $region;
  /**
   * URI of a VPN gateway at remote end of the tunnel.
   *
   * @var string
   */
  public $remoteGateway;
  /**
   * Remote VPN gateway's IP address.
   *
   * @var string
   */
  public $remoteGatewayIp;
  /**
   * Type of the routing policy.
   *
   * @var string
   */
  public $routingType;
  /**
   * URI of the VPN gateway at local end of the tunnel.
   *
   * @var string
   */
  public $sourceGateway;
  /**
   * Local VPN gateway's IP address.
   *
   * @var string
   */
  public $sourceGatewayIp;
  /**
   * URI of a VPN tunnel.
   *
   * @var string
   */
  public $uri;

  /**
   * Name of a VPN tunnel.
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
   * URI of a Compute Engine network where the VPN tunnel is configured.
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
   * Name of a Google Cloud region where this VPN tunnel is configured.
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
   * URI of a VPN gateway at remote end of the tunnel.
   *
   * @param string $remoteGateway
   */
  public function setRemoteGateway($remoteGateway)
  {
    $this->remoteGateway = $remoteGateway;
  }
  /**
   * @return string
   */
  public function getRemoteGateway()
  {
    return $this->remoteGateway;
  }
  /**
   * Remote VPN gateway's IP address.
   *
   * @param string $remoteGatewayIp
   */
  public function setRemoteGatewayIp($remoteGatewayIp)
  {
    $this->remoteGatewayIp = $remoteGatewayIp;
  }
  /**
   * @return string
   */
  public function getRemoteGatewayIp()
  {
    return $this->remoteGatewayIp;
  }
  /**
   * Type of the routing policy.
   *
   * Accepted values: ROUTING_TYPE_UNSPECIFIED, ROUTE_BASED, POLICY_BASED,
   * DYNAMIC
   *
   * @param self::ROUTING_TYPE_* $routingType
   */
  public function setRoutingType($routingType)
  {
    $this->routingType = $routingType;
  }
  /**
   * @return self::ROUTING_TYPE_*
   */
  public function getRoutingType()
  {
    return $this->routingType;
  }
  /**
   * URI of the VPN gateway at local end of the tunnel.
   *
   * @param string $sourceGateway
   */
  public function setSourceGateway($sourceGateway)
  {
    $this->sourceGateway = $sourceGateway;
  }
  /**
   * @return string
   */
  public function getSourceGateway()
  {
    return $this->sourceGateway;
  }
  /**
   * Local VPN gateway's IP address.
   *
   * @param string $sourceGatewayIp
   */
  public function setSourceGatewayIp($sourceGatewayIp)
  {
    $this->sourceGatewayIp = $sourceGatewayIp;
  }
  /**
   * @return string
   */
  public function getSourceGatewayIp()
  {
    return $this->sourceGatewayIp;
  }
  /**
   * URI of a VPN tunnel.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpnTunnelInfo::class, 'Google_Service_NetworkManagement_VpnTunnelInfo');
