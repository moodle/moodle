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

class RouterStatusBgpPeerStatus extends \Google\Collection
{
  public const STATUS_DOWN = 'DOWN';
  public const STATUS_UNKNOWN = 'UNKNOWN';
  public const STATUS_UP = 'UP';
  /**
   * BGP peer disabled because it requires IPv4 but the underlying connection is
   * IPv6-only.
   */
  public const STATUS_REASON_IPV4_PEER_ON_IPV6_ONLY_CONNECTION = 'IPV4_PEER_ON_IPV6_ONLY_CONNECTION';
  /**
   * BGP peer disabled because it requires IPv6 but the underlying connection is
   * IPv4-only.
   */
  public const STATUS_REASON_IPV6_PEER_ON_IPV4_ONLY_CONNECTION = 'IPV6_PEER_ON_IPV4_ONLY_CONNECTION';
  /**
   * Indicates internal problems with configuration of MD5 authentication. This
   * particular reason can only be returned when md5AuthEnabled is true and
   * status is DOWN.
   */
  public const STATUS_REASON_MD5_AUTH_INTERNAL_PROBLEM = 'MD5_AUTH_INTERNAL_PROBLEM';
  public const STATUS_REASON_STATUS_REASON_UNSPECIFIED = 'STATUS_REASON_UNSPECIFIED';
  protected $collection_key = 'advertisedRoutes';
  protected $advertisedRoutesType = Route::class;
  protected $advertisedRoutesDataType = 'array';
  protected $bfdStatusType = BfdStatus::class;
  protected $bfdStatusDataType = '';
  /**
   * Output only. Enable IPv4 traffic over BGP Peer. It is enabled by default if
   * the peerIpAddress is version 4.
   *
   * @var bool
   */
  public $enableIpv4;
  /**
   * Output only. Enable IPv6 traffic over BGP Peer. It is enabled by default if
   * the peerIpAddress is version 6.
   *
   * @var bool
   */
  public $enableIpv6;
  /**
   * Output only. IP address of the local BGP interface.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Output only. IPv4 address of the local BGP interface.
   *
   * @var string
   */
  public $ipv4NexthopAddress;
  /**
   * Output only. IPv6 address of the local BGP interface.
   *
   * @var string
   */
  public $ipv6NexthopAddress;
  /**
   * Output only. URL of the VPN tunnel that this BGP peer controls.
   *
   * @var string
   */
  public $linkedVpnTunnel;
  /**
   * Informs whether MD5 authentication is enabled on this BGP peer.
   *
   * @var bool
   */
  public $md5AuthEnabled;
  /**
   * Output only. Name of this BGP peer. Unique within the Routers resource.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Number of routes learned from the remote BGP Peer.
   *
   * @var string
   */
  public $numLearnedRoutes;
  /**
   * Output only. IP address of the remote BGP interface.
   *
   * @var string
   */
  public $peerIpAddress;
  /**
   * Output only. IPv4 address of the remote BGP interface.
   *
   * @var string
   */
  public $peerIpv4NexthopAddress;
  /**
   * Output only. IPv6 address of the remote BGP interface.
   *
   * @var string
   */
  public $peerIpv6NexthopAddress;
  /**
   * Output only. [Output only] URI of the VM instance that is used as third-
   * party router appliances such as Next Gen Firewalls, Virtual Routers, or
   * Router Appliances. The VM instance is the peer side of the BGP session.
   *
   * @var string
   */
  public $routerApplianceInstance;
  /**
   * Output only. The state of the BGP session. For a list of possible values
   * for this field, seeBGP session states.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Status of the BGP peer: {UP, DOWN}
   *
   * @var string
   */
  public $status;
  /**
   * Indicates why particular status was returned.
   *
   * @var string
   */
  public $statusReason;
  /**
   * Output only. Time this session has been up. Format:  14 years, 51 weeks, 6
   * days, 23 hours, 59 minutes, 59 seconds
   *
   * @var string
   */
  public $uptime;
  /**
   * Output only. Time this session has been up, in seconds. Format:  145
   *
   * @var string
   */
  public $uptimeSeconds;

  /**
   * Routes that were advertised to the remote BGP peer
   *
   * @param Route[] $advertisedRoutes
   */
  public function setAdvertisedRoutes($advertisedRoutes)
  {
    $this->advertisedRoutes = $advertisedRoutes;
  }
  /**
   * @return Route[]
   */
  public function getAdvertisedRoutes()
  {
    return $this->advertisedRoutes;
  }
  /**
   * @param BfdStatus $bfdStatus
   */
  public function setBfdStatus(BfdStatus $bfdStatus)
  {
    $this->bfdStatus = $bfdStatus;
  }
  /**
   * @return BfdStatus
   */
  public function getBfdStatus()
  {
    return $this->bfdStatus;
  }
  /**
   * Output only. Enable IPv4 traffic over BGP Peer. It is enabled by default if
   * the peerIpAddress is version 4.
   *
   * @param bool $enableIpv4
   */
  public function setEnableIpv4($enableIpv4)
  {
    $this->enableIpv4 = $enableIpv4;
  }
  /**
   * @return bool
   */
  public function getEnableIpv4()
  {
    return $this->enableIpv4;
  }
  /**
   * Output only. Enable IPv6 traffic over BGP Peer. It is enabled by default if
   * the peerIpAddress is version 6.
   *
   * @param bool $enableIpv6
   */
  public function setEnableIpv6($enableIpv6)
  {
    $this->enableIpv6 = $enableIpv6;
  }
  /**
   * @return bool
   */
  public function getEnableIpv6()
  {
    return $this->enableIpv6;
  }
  /**
   * Output only. IP address of the local BGP interface.
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
   * Output only. IPv4 address of the local BGP interface.
   *
   * @param string $ipv4NexthopAddress
   */
  public function setIpv4NexthopAddress($ipv4NexthopAddress)
  {
    $this->ipv4NexthopAddress = $ipv4NexthopAddress;
  }
  /**
   * @return string
   */
  public function getIpv4NexthopAddress()
  {
    return $this->ipv4NexthopAddress;
  }
  /**
   * Output only. IPv6 address of the local BGP interface.
   *
   * @param string $ipv6NexthopAddress
   */
  public function setIpv6NexthopAddress($ipv6NexthopAddress)
  {
    $this->ipv6NexthopAddress = $ipv6NexthopAddress;
  }
  /**
   * @return string
   */
  public function getIpv6NexthopAddress()
  {
    return $this->ipv6NexthopAddress;
  }
  /**
   * Output only. URL of the VPN tunnel that this BGP peer controls.
   *
   * @param string $linkedVpnTunnel
   */
  public function setLinkedVpnTunnel($linkedVpnTunnel)
  {
    $this->linkedVpnTunnel = $linkedVpnTunnel;
  }
  /**
   * @return string
   */
  public function getLinkedVpnTunnel()
  {
    return $this->linkedVpnTunnel;
  }
  /**
   * Informs whether MD5 authentication is enabled on this BGP peer.
   *
   * @param bool $md5AuthEnabled
   */
  public function setMd5AuthEnabled($md5AuthEnabled)
  {
    $this->md5AuthEnabled = $md5AuthEnabled;
  }
  /**
   * @return bool
   */
  public function getMd5AuthEnabled()
  {
    return $this->md5AuthEnabled;
  }
  /**
   * Output only. Name of this BGP peer. Unique within the Routers resource.
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
   * Output only. Number of routes learned from the remote BGP Peer.
   *
   * @param string $numLearnedRoutes
   */
  public function setNumLearnedRoutes($numLearnedRoutes)
  {
    $this->numLearnedRoutes = $numLearnedRoutes;
  }
  /**
   * @return string
   */
  public function getNumLearnedRoutes()
  {
    return $this->numLearnedRoutes;
  }
  /**
   * Output only. IP address of the remote BGP interface.
   *
   * @param string $peerIpAddress
   */
  public function setPeerIpAddress($peerIpAddress)
  {
    $this->peerIpAddress = $peerIpAddress;
  }
  /**
   * @return string
   */
  public function getPeerIpAddress()
  {
    return $this->peerIpAddress;
  }
  /**
   * Output only. IPv4 address of the remote BGP interface.
   *
   * @param string $peerIpv4NexthopAddress
   */
  public function setPeerIpv4NexthopAddress($peerIpv4NexthopAddress)
  {
    $this->peerIpv4NexthopAddress = $peerIpv4NexthopAddress;
  }
  /**
   * @return string
   */
  public function getPeerIpv4NexthopAddress()
  {
    return $this->peerIpv4NexthopAddress;
  }
  /**
   * Output only. IPv6 address of the remote BGP interface.
   *
   * @param string $peerIpv6NexthopAddress
   */
  public function setPeerIpv6NexthopAddress($peerIpv6NexthopAddress)
  {
    $this->peerIpv6NexthopAddress = $peerIpv6NexthopAddress;
  }
  /**
   * @return string
   */
  public function getPeerIpv6NexthopAddress()
  {
    return $this->peerIpv6NexthopAddress;
  }
  /**
   * Output only. [Output only] URI of the VM instance that is used as third-
   * party router appliances such as Next Gen Firewalls, Virtual Routers, or
   * Router Appliances. The VM instance is the peer side of the BGP session.
   *
   * @param string $routerApplianceInstance
   */
  public function setRouterApplianceInstance($routerApplianceInstance)
  {
    $this->routerApplianceInstance = $routerApplianceInstance;
  }
  /**
   * @return string
   */
  public function getRouterApplianceInstance()
  {
    return $this->routerApplianceInstance;
  }
  /**
   * Output only. The state of the BGP session. For a list of possible values
   * for this field, seeBGP session states.
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Status of the BGP peer: {UP, DOWN}
   *
   * Accepted values: DOWN, UNKNOWN, UP
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Indicates why particular status was returned.
   *
   * Accepted values: IPV4_PEER_ON_IPV6_ONLY_CONNECTION,
   * IPV6_PEER_ON_IPV4_ONLY_CONNECTION, MD5_AUTH_INTERNAL_PROBLEM,
   * STATUS_REASON_UNSPECIFIED
   *
   * @param self::STATUS_REASON_* $statusReason
   */
  public function setStatusReason($statusReason)
  {
    $this->statusReason = $statusReason;
  }
  /**
   * @return self::STATUS_REASON_*
   */
  public function getStatusReason()
  {
    return $this->statusReason;
  }
  /**
   * Output only. Time this session has been up. Format:  14 years, 51 weeks, 6
   * days, 23 hours, 59 minutes, 59 seconds
   *
   * @param string $uptime
   */
  public function setUptime($uptime)
  {
    $this->uptime = $uptime;
  }
  /**
   * @return string
   */
  public function getUptime()
  {
    return $this->uptime;
  }
  /**
   * Output only. Time this session has been up, in seconds. Format:  145
   *
   * @param string $uptimeSeconds
   */
  public function setUptimeSeconds($uptimeSeconds)
  {
    $this->uptimeSeconds = $uptimeSeconds;
  }
  /**
   * @return string
   */
  public function getUptimeSeconds()
  {
    return $this->uptimeSeconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RouterStatusBgpPeerStatus::class, 'Google_Service_Compute_RouterStatusBgpPeerStatus');
