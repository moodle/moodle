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

class VpnGatewayStatusVpnConnection extends \Google\Collection
{
  protected $collection_key = 'tunnels';
  /**
   * Output only. URL reference to the peer external VPN gateways to which the
   * VPN tunnels in this VPN connection are connected. This field is mutually
   * exclusive with peer_gcp_gateway.
   *
   * @var string
   */
  public $peerExternalGateway;
  /**
   * Output only. URL reference to the peer side VPN gateways to which the VPN
   * tunnels in this VPN connection are connected. This field is mutually
   * exclusive with peer_gcp_gateway.
   *
   * @var string
   */
  public $peerGcpGateway;
  protected $stateType = VpnGatewayStatusHighAvailabilityRequirementState::class;
  protected $stateDataType = '';
  protected $tunnelsType = VpnGatewayStatusTunnel::class;
  protected $tunnelsDataType = 'array';

  /**
   * Output only. URL reference to the peer external VPN gateways to which the
   * VPN tunnels in this VPN connection are connected. This field is mutually
   * exclusive with peer_gcp_gateway.
   *
   * @param string $peerExternalGateway
   */
  public function setPeerExternalGateway($peerExternalGateway)
  {
    $this->peerExternalGateway = $peerExternalGateway;
  }
  /**
   * @return string
   */
  public function getPeerExternalGateway()
  {
    return $this->peerExternalGateway;
  }
  /**
   * Output only. URL reference to the peer side VPN gateways to which the VPN
   * tunnels in this VPN connection are connected. This field is mutually
   * exclusive with peer_gcp_gateway.
   *
   * @param string $peerGcpGateway
   */
  public function setPeerGcpGateway($peerGcpGateway)
  {
    $this->peerGcpGateway = $peerGcpGateway;
  }
  /**
   * @return string
   */
  public function getPeerGcpGateway()
  {
    return $this->peerGcpGateway;
  }
  /**
   * HighAvailabilityRequirementState for the VPN connection.
   *
   * @param VpnGatewayStatusHighAvailabilityRequirementState $state
   */
  public function setState(VpnGatewayStatusHighAvailabilityRequirementState $state)
  {
    $this->state = $state;
  }
  /**
   * @return VpnGatewayStatusHighAvailabilityRequirementState
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * List of VPN tunnels that are in this VPN connection.
   *
   * @param VpnGatewayStatusTunnel[] $tunnels
   */
  public function setTunnels($tunnels)
  {
    $this->tunnels = $tunnels;
  }
  /**
   * @return VpnGatewayStatusTunnel[]
   */
  public function getTunnels()
  {
    return $this->tunnels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpnGatewayStatusVpnConnection::class, 'Google_Service_Compute_VpnGatewayStatusVpnConnection');
