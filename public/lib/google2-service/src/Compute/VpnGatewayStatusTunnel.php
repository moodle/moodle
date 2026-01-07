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

class VpnGatewayStatusTunnel extends \Google\Model
{
  /**
   * Output only. The VPN gateway interface this VPN tunnel is associated with.
   *
   * @var string
   */
  public $localGatewayInterface;
  /**
   * Output only. The peer gateway interface this VPN tunnel is connected to,
   * the peer gateway could either be an external VPN gateway or a Google Cloud
   * VPN gateway.
   *
   * @var string
   */
  public $peerGatewayInterface;
  /**
   * Output only. URL reference to the VPN tunnel.
   *
   * @var string
   */
  public $tunnelUrl;

  /**
   * Output only. The VPN gateway interface this VPN tunnel is associated with.
   *
   * @param string $localGatewayInterface
   */
  public function setLocalGatewayInterface($localGatewayInterface)
  {
    $this->localGatewayInterface = $localGatewayInterface;
  }
  /**
   * @return string
   */
  public function getLocalGatewayInterface()
  {
    return $this->localGatewayInterface;
  }
  /**
   * Output only. The peer gateway interface this VPN tunnel is connected to,
   * the peer gateway could either be an external VPN gateway or a Google Cloud
   * VPN gateway.
   *
   * @param string $peerGatewayInterface
   */
  public function setPeerGatewayInterface($peerGatewayInterface)
  {
    $this->peerGatewayInterface = $peerGatewayInterface;
  }
  /**
   * @return string
   */
  public function getPeerGatewayInterface()
  {
    return $this->peerGatewayInterface;
  }
  /**
   * Output only. URL reference to the VPN tunnel.
   *
   * @param string $tunnelUrl
   */
  public function setTunnelUrl($tunnelUrl)
  {
    $this->tunnelUrl = $tunnelUrl;
  }
  /**
   * @return string
   */
  public function getTunnelUrl()
  {
    return $this->tunnelUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VpnGatewayStatusTunnel::class, 'Google_Service_Compute_VpnGatewayStatusTunnel');
