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

namespace Google\Service\GKEOnPrem;

class BareMetalBgpPeerConfig extends \Google\Collection
{
  protected $collection_key = 'controlPlaneNodes';
  /**
   * Required. BGP autonomous system number (ASN) for the network that contains
   * the external peer device.
   *
   * @var string
   */
  public $asn;
  /**
   * The IP address of the control plane node that connects to the external
   * peer. If you don't specify any control plane nodes, all control plane nodes
   * can connect to the external peer. If you specify one or more IP addresses,
   * only the nodes specified participate in peering sessions.
   *
   * @var string[]
   */
  public $controlPlaneNodes;
  /**
   * Required. The IP address of the external peer device.
   *
   * @var string
   */
  public $ipAddress;

  /**
   * Required. BGP autonomous system number (ASN) for the network that contains
   * the external peer device.
   *
   * @param string $asn
   */
  public function setAsn($asn)
  {
    $this->asn = $asn;
  }
  /**
   * @return string
   */
  public function getAsn()
  {
    return $this->asn;
  }
  /**
   * The IP address of the control plane node that connects to the external
   * peer. If you don't specify any control plane nodes, all control plane nodes
   * can connect to the external peer. If you specify one or more IP addresses,
   * only the nodes specified participate in peering sessions.
   *
   * @param string[] $controlPlaneNodes
   */
  public function setControlPlaneNodes($controlPlaneNodes)
  {
    $this->controlPlaneNodes = $controlPlaneNodes;
  }
  /**
   * @return string[]
   */
  public function getControlPlaneNodes()
  {
    return $this->controlPlaneNodes;
  }
  /**
   * Required. The IP address of the external peer device.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalBgpPeerConfig::class, 'Google_Service_GKEOnPrem_BareMetalBgpPeerConfig');
