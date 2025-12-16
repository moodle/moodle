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

class ProxyConnectionInfo extends \Google\Model
{
  /**
   * URI of the network where connection is proxied.
   *
   * @var string
   */
  public $networkUri;
  /**
   * Destination IP address of a new connection.
   *
   * @var string
   */
  public $newDestinationIp;
  /**
   * Destination port of a new connection. Only valid when protocol is TCP or
   * UDP.
   *
   * @var int
   */
  public $newDestinationPort;
  /**
   * Source IP address of a new connection.
   *
   * @var string
   */
  public $newSourceIp;
  /**
   * Source port of a new connection. Only valid when protocol is TCP or UDP.
   *
   * @var int
   */
  public $newSourcePort;
  /**
   * Destination IP address of an original connection
   *
   * @var string
   */
  public $oldDestinationIp;
  /**
   * Destination port of an original connection. Only valid when protocol is TCP
   * or UDP.
   *
   * @var int
   */
  public $oldDestinationPort;
  /**
   * Source IP address of an original connection.
   *
   * @var string
   */
  public $oldSourceIp;
  /**
   * Source port of an original connection. Only valid when protocol is TCP or
   * UDP.
   *
   * @var int
   */
  public $oldSourcePort;
  /**
   * IP protocol in string format, for example: "TCP", "UDP", "ICMP".
   *
   * @var string
   */
  public $protocol;
  /**
   * Uri of proxy subnet.
   *
   * @var string
   */
  public $subnetUri;

  /**
   * URI of the network where connection is proxied.
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
   * Destination IP address of a new connection.
   *
   * @param string $newDestinationIp
   */
  public function setNewDestinationIp($newDestinationIp)
  {
    $this->newDestinationIp = $newDestinationIp;
  }
  /**
   * @return string
   */
  public function getNewDestinationIp()
  {
    return $this->newDestinationIp;
  }
  /**
   * Destination port of a new connection. Only valid when protocol is TCP or
   * UDP.
   *
   * @param int $newDestinationPort
   */
  public function setNewDestinationPort($newDestinationPort)
  {
    $this->newDestinationPort = $newDestinationPort;
  }
  /**
   * @return int
   */
  public function getNewDestinationPort()
  {
    return $this->newDestinationPort;
  }
  /**
   * Source IP address of a new connection.
   *
   * @param string $newSourceIp
   */
  public function setNewSourceIp($newSourceIp)
  {
    $this->newSourceIp = $newSourceIp;
  }
  /**
   * @return string
   */
  public function getNewSourceIp()
  {
    return $this->newSourceIp;
  }
  /**
   * Source port of a new connection. Only valid when protocol is TCP or UDP.
   *
   * @param int $newSourcePort
   */
  public function setNewSourcePort($newSourcePort)
  {
    $this->newSourcePort = $newSourcePort;
  }
  /**
   * @return int
   */
  public function getNewSourcePort()
  {
    return $this->newSourcePort;
  }
  /**
   * Destination IP address of an original connection
   *
   * @param string $oldDestinationIp
   */
  public function setOldDestinationIp($oldDestinationIp)
  {
    $this->oldDestinationIp = $oldDestinationIp;
  }
  /**
   * @return string
   */
  public function getOldDestinationIp()
  {
    return $this->oldDestinationIp;
  }
  /**
   * Destination port of an original connection. Only valid when protocol is TCP
   * or UDP.
   *
   * @param int $oldDestinationPort
   */
  public function setOldDestinationPort($oldDestinationPort)
  {
    $this->oldDestinationPort = $oldDestinationPort;
  }
  /**
   * @return int
   */
  public function getOldDestinationPort()
  {
    return $this->oldDestinationPort;
  }
  /**
   * Source IP address of an original connection.
   *
   * @param string $oldSourceIp
   */
  public function setOldSourceIp($oldSourceIp)
  {
    $this->oldSourceIp = $oldSourceIp;
  }
  /**
   * @return string
   */
  public function getOldSourceIp()
  {
    return $this->oldSourceIp;
  }
  /**
   * Source port of an original connection. Only valid when protocol is TCP or
   * UDP.
   *
   * @param int $oldSourcePort
   */
  public function setOldSourcePort($oldSourcePort)
  {
    $this->oldSourcePort = $oldSourcePort;
  }
  /**
   * @return int
   */
  public function getOldSourcePort()
  {
    return $this->oldSourcePort;
  }
  /**
   * IP protocol in string format, for example: "TCP", "UDP", "ICMP".
   *
   * @param string $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return string
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * Uri of proxy subnet.
   *
   * @param string $subnetUri
   */
  public function setSubnetUri($subnetUri)
  {
    $this->subnetUri = $subnetUri;
  }
  /**
   * @return string
   */
  public function getSubnetUri()
  {
    return $this->subnetUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProxyConnectionInfo::class, 'Google_Service_NetworkManagement_ProxyConnectionInfo');
