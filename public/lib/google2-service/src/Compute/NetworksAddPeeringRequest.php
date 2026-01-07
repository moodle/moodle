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

class NetworksAddPeeringRequest extends \Google\Model
{
  /**
   * This field will be deprecated soon. Useexchange_subnet_routes in
   * network_peering instead. Indicates whether full mesh connectivity is
   * created and managed automatically between peered networks. Currently this
   * field should always be true since Google Compute Engine will automatically
   * create and manage subnetwork routes between two networks when peering state
   * isACTIVE.
   *
   * @var bool
   */
  public $autoCreateRoutes;
  /**
   * Name of the peering, which should conform to RFC1035.
   *
   * @var string
   */
  public $name;
  protected $networkPeeringType = NetworkPeering::class;
  protected $networkPeeringDataType = '';
  /**
   * URL of the peer network.  It can be either full URL or partial URL. The
   * peer network may belong to a different project. If the partial URL does not
   * contain project, it is assumed that the peer network is in the same project
   * as the current network.
   *
   * @var string
   */
  public $peerNetwork;

  /**
   * This field will be deprecated soon. Useexchange_subnet_routes in
   * network_peering instead. Indicates whether full mesh connectivity is
   * created and managed automatically between peered networks. Currently this
   * field should always be true since Google Compute Engine will automatically
   * create and manage subnetwork routes between two networks when peering state
   * isACTIVE.
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
   * Name of the peering, which should conform to RFC1035.
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
   * Network peering parameters. In order to specify route policies for peering
   * using import and export custom routes, you must specify all peering related
   * parameters (name, peer network,exchange_subnet_routes) in the
   * network_peering field. The corresponding fields in
   * NetworksAddPeeringRequest will be deprecated soon.
   *
   * @param NetworkPeering $networkPeering
   */
  public function setNetworkPeering(NetworkPeering $networkPeering)
  {
    $this->networkPeering = $networkPeering;
  }
  /**
   * @return NetworkPeering
   */
  public function getNetworkPeering()
  {
    return $this->networkPeering;
  }
  /**
   * URL of the peer network.  It can be either full URL or partial URL. The
   * peer network may belong to a different project. If the partial URL does not
   * contain project, it is assumed that the peer network is in the same project
   * as the current network.
   *
   * @param string $peerNetwork
   */
  public function setPeerNetwork($peerNetwork)
  {
    $this->peerNetwork = $peerNetwork;
  }
  /**
   * @return string
   */
  public function getPeerNetwork()
  {
    return $this->peerNetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworksAddPeeringRequest::class, 'Google_Service_Compute_NetworksAddPeeringRequest');
