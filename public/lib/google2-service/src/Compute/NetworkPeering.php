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

class NetworkPeering extends \Google\Model
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
   * Matching configuration exists on the peer.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * There is no matching configuration on the peer, including the case when
   * peer does not exist.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * Updates are reflected in the local peering but aren't applied to the
   * peering connection until a complementary change is made to the matching
   * peering. To delete a peering with the consensus update strategy, both the
   * peerings must request the deletion of the peering before the peering can be
   * deleted.
   */
  public const UPDATE_STRATEGY_CONSENSUS = 'CONSENSUS';
  /**
   * In this mode, changes to the peering configuration can be unilaterally
   * altered by changing either side of the peering. This is the default value
   * if the field is unspecified.
   */
  public const UPDATE_STRATEGY_INDEPENDENT = 'INDEPENDENT';
  /**
   * Peerings with update strategy UNSPECIFIED are created with update strategy
   * INDEPENDENT.
   */
  public const UPDATE_STRATEGY_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * This field will be deprecated soon. Use theexchange_subnet_routes field
   * instead. Indicates whether full mesh connectivity is created and managed
   * automatically between peered networks. Currently this field should always
   * be true since Google Compute Engine will automatically create and manage
   * subnetwork routes between two networks when peering state isACTIVE.
   *
   * @var bool
   */
  public $autoCreateRoutes;
  protected $connectionStatusType = NetworkPeeringConnectionStatus::class;
  protected $connectionStatusDataType = '';
  /**
   * Indicates whether full mesh connectivity is created and managed
   * automatically between peered networks. Currently this field should always
   * be true since Google Compute Engine will automatically create and manage
   * subnetwork routes between two networks when peering state isACTIVE.
   *
   * @var bool
   */
  public $exchangeSubnetRoutes;
  /**
   * Whether to export the custom routes to peer network. The default value is
   * false.
   *
   * @var bool
   */
  public $exportCustomRoutes;
  /**
   * Whether subnet routes with public IP range are exported. The default value
   * is true, all subnet routes are exported.IPv4 special-use ranges are always
   * exported to peers and are not controlled by this field.
   *
   * @var bool
   */
  public $exportSubnetRoutesWithPublicIp;
  /**
   * Whether to import the custom routes from peer network. The default value is
   * false.
   *
   * @var bool
   */
  public $importCustomRoutes;
  /**
   * Whether subnet routes with public IP range are imported. The default value
   * is false.IPv4 special-use ranges are always imported from peers and are not
   * controlled by this field.
   *
   * @var bool
   */
  public $importSubnetRoutesWithPublicIp;
  /**
   * Name of this peering. Provided by the client when the peering is created.
   * The name must comply withRFC1035. Specifically, the name must be 1-63
   * characters long and match regular expression `[a-z]([-a-z0-9]*[a-z0-9])?`.
   * The first character must be a lowercase letter, and all the following
   * characters must be a dash, lowercase letter, or digit, except the last
   * character, which cannot be a dash.
   *
   * @var string
   */
  public $name;
  /**
   * The URL of the peer network. It can be either full URL or partial URL. The
   * peer network may belong to a different project. If the partial URL does not
   * contain project, it is assumed that the peer network is in the same project
   * as the current network.
   *
   * @var string
   */
  public $network;
  /**
   * Output only. [Output Only] Maximum Transmission Unit in bytes of the peer
   * network.
   *
   * @var int
   */
  public $peerMtu;
  /**
   * Which IP version(s) of traffic and routes are allowed to be imported or
   * exported between peer networks. The default value is IPV4_ONLY.
   *
   * @var string
   */
  public $stackType;
  /**
   * Output only. [Output Only] State for the peering, either `ACTIVE` or
   * `INACTIVE`. The peering is `ACTIVE` when there's a matching configuration
   * in the peer network.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. [Output Only] Details about the current state of the peering.
   *
   * @var string
   */
  public $stateDetails;
  /**
   * The update strategy determines the semantics for updates and deletes to the
   * peering connection configuration.
   *
   * @var string
   */
  public $updateStrategy;

  /**
   * This field will be deprecated soon. Use theexchange_subnet_routes field
   * instead. Indicates whether full mesh connectivity is created and managed
   * automatically between peered networks. Currently this field should always
   * be true since Google Compute Engine will automatically create and manage
   * subnetwork routes between two networks when peering state isACTIVE.
   *
   * @param bool $autoCreateRoutes
   */
  public function setAutoCreateRoutes($autoCreateRoutes)
  {
    $this->autoCreateRoutes = $autoCreateRoutes;
  }
  /**
   * @return bool
   */
  public function getAutoCreateRoutes()
  {
    return $this->autoCreateRoutes;
  }
  /**
   * Output only. [Output Only] The effective state of the peering connection as
   * a whole.
   *
   * @param NetworkPeeringConnectionStatus $connectionStatus
   */
  public function setConnectionStatus(NetworkPeeringConnectionStatus $connectionStatus)
  {
    $this->connectionStatus = $connectionStatus;
  }
  /**
   * @return NetworkPeeringConnectionStatus
   */
  public function getConnectionStatus()
  {
    return $this->connectionStatus;
  }
  /**
   * Indicates whether full mesh connectivity is created and managed
   * automatically between peered networks. Currently this field should always
   * be true since Google Compute Engine will automatically create and manage
   * subnetwork routes between two networks when peering state isACTIVE.
   *
   * @param bool $exchangeSubnetRoutes
   */
  public function setExchangeSubnetRoutes($exchangeSubnetRoutes)
  {
    $this->exchangeSubnetRoutes = $exchangeSubnetRoutes;
  }
  /**
   * @return bool
   */
  public function getExchangeSubnetRoutes()
  {
    return $this->exchangeSubnetRoutes;
  }
  /**
   * Whether to export the custom routes to peer network. The default value is
   * false.
   *
   * @param bool $exportCustomRoutes
   */
  public function setExportCustomRoutes($exportCustomRoutes)
  {
    $this->exportCustomRoutes = $exportCustomRoutes;
  }
  /**
   * @return bool
   */
  public function getExportCustomRoutes()
  {
    return $this->exportCustomRoutes;
  }
  /**
   * Whether subnet routes with public IP range are exported. The default value
   * is true, all subnet routes are exported.IPv4 special-use ranges are always
   * exported to peers and are not controlled by this field.
   *
   * @param bool $exportSubnetRoutesWithPublicIp
   */
  public function setExportSubnetRoutesWithPublicIp($exportSubnetRoutesWithPublicIp)
  {
    $this->exportSubnetRoutesWithPublicIp = $exportSubnetRoutesWithPublicIp;
  }
  /**
   * @return bool
   */
  public function getExportSubnetRoutesWithPublicIp()
  {
    return $this->exportSubnetRoutesWithPublicIp;
  }
  /**
   * Whether to import the custom routes from peer network. The default value is
   * false.
   *
   * @param bool $importCustomRoutes
   */
  public function setImportCustomRoutes($importCustomRoutes)
  {
    $this->importCustomRoutes = $importCustomRoutes;
  }
  /**
   * @return bool
   */
  public function getImportCustomRoutes()
  {
    return $this->importCustomRoutes;
  }
  /**
   * Whether subnet routes with public IP range are imported. The default value
   * is false.IPv4 special-use ranges are always imported from peers and are not
   * controlled by this field.
   *
   * @param bool $importSubnetRoutesWithPublicIp
   */
  public function setImportSubnetRoutesWithPublicIp($importSubnetRoutesWithPublicIp)
  {
    $this->importSubnetRoutesWithPublicIp = $importSubnetRoutesWithPublicIp;
  }
  /**
   * @return bool
   */
  public function getImportSubnetRoutesWithPublicIp()
  {
    return $this->importSubnetRoutesWithPublicIp;
  }
  /**
   * Name of this peering. Provided by the client when the peering is created.
   * The name must comply withRFC1035. Specifically, the name must be 1-63
   * characters long and match regular expression `[a-z]([-a-z0-9]*[a-z0-9])?`.
   * The first character must be a lowercase letter, and all the following
   * characters must be a dash, lowercase letter, or digit, except the last
   * character, which cannot be a dash.
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
   * The URL of the peer network. It can be either full URL or partial URL. The
   * peer network may belong to a different project. If the partial URL does not
   * contain project, it is assumed that the peer network is in the same project
   * as the current network.
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
   * Output only. [Output Only] Maximum Transmission Unit in bytes of the peer
   * network.
   *
   * @param int $peerMtu
   */
  public function setPeerMtu($peerMtu)
  {
    $this->peerMtu = $peerMtu;
  }
  /**
   * @return int
   */
  public function getPeerMtu()
  {
    return $this->peerMtu;
  }
  /**
   * Which IP version(s) of traffic and routes are allowed to be imported or
   * exported between peer networks. The default value is IPV4_ONLY.
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
  /**
   * Output only. [Output Only] State for the peering, either `ACTIVE` or
   * `INACTIVE`. The peering is `ACTIVE` when there's a matching configuration
   * in the peer network.
   *
   * Accepted values: ACTIVE, INACTIVE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. [Output Only] Details about the current state of the peering.
   *
   * @param string $stateDetails
   */
  public function setStateDetails($stateDetails)
  {
    $this->stateDetails = $stateDetails;
  }
  /**
   * @return string
   */
  public function getStateDetails()
  {
    return $this->stateDetails;
  }
  /**
   * The update strategy determines the semantics for updates and deletes to the
   * peering connection configuration.
   *
   * Accepted values: CONSENSUS, INDEPENDENT, UNSPECIFIED
   *
   * @param self::UPDATE_STRATEGY_* $updateStrategy
   */
  public function setUpdateStrategy($updateStrategy)
  {
    $this->updateStrategy = $updateStrategy;
  }
  /**
   * @return self::UPDATE_STRATEGY_*
   */
  public function getUpdateStrategy()
  {
    return $this->updateStrategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkPeering::class, 'Google_Service_Compute_NetworkPeering');
