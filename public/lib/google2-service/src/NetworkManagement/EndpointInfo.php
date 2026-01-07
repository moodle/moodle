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

class EndpointInfo extends \Google\Model
{
  /**
   * Destination IP address.
   *
   * @var string
   */
  public $destinationIp;
  /**
   * URI of the network where this packet is sent to.
   *
   * @var string
   */
  public $destinationNetworkUri;
  /**
   * Destination port. Only valid when protocol is TCP or UDP.
   *
   * @var int
   */
  public $destinationPort;
  /**
   * IP protocol in string format, for example: "TCP", "UDP", "ICMP".
   *
   * @var string
   */
  public $protocol;
  /**
   * URI of the source telemetry agent this packet originates from.
   *
   * @var string
   */
  public $sourceAgentUri;
  /**
   * Source IP address.
   *
   * @var string
   */
  public $sourceIp;
  /**
   * URI of the network where this packet originates from.
   *
   * @var string
   */
  public $sourceNetworkUri;
  /**
   * Source port. Only valid when protocol is TCP or UDP.
   *
   * @var int
   */
  public $sourcePort;

  /**
   * Destination IP address.
   *
   * @param string $destinationIp
   */
  public function setDestinationIp($destinationIp)
  {
    $this->destinationIp = $destinationIp;
  }
  /**
   * @return string
   */
  public function getDestinationIp()
  {
    return $this->destinationIp;
  }
  /**
   * URI of the network where this packet is sent to.
   *
   * @param string $destinationNetworkUri
   */
  public function setDestinationNetworkUri($destinationNetworkUri)
  {
    $this->destinationNetworkUri = $destinationNetworkUri;
  }
  /**
   * @return string
   */
  public function getDestinationNetworkUri()
  {
    return $this->destinationNetworkUri;
  }
  /**
   * Destination port. Only valid when protocol is TCP or UDP.
   *
   * @param int $destinationPort
   */
  public function setDestinationPort($destinationPort)
  {
    $this->destinationPort = $destinationPort;
  }
  /**
   * @return int
   */
  public function getDestinationPort()
  {
    return $this->destinationPort;
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
   * URI of the source telemetry agent this packet originates from.
   *
   * @param string $sourceAgentUri
   */
  public function setSourceAgentUri($sourceAgentUri)
  {
    $this->sourceAgentUri = $sourceAgentUri;
  }
  /**
   * @return string
   */
  public function getSourceAgentUri()
  {
    return $this->sourceAgentUri;
  }
  /**
   * Source IP address.
   *
   * @param string $sourceIp
   */
  public function setSourceIp($sourceIp)
  {
    $this->sourceIp = $sourceIp;
  }
  /**
   * @return string
   */
  public function getSourceIp()
  {
    return $this->sourceIp;
  }
  /**
   * URI of the network where this packet originates from.
   *
   * @param string $sourceNetworkUri
   */
  public function setSourceNetworkUri($sourceNetworkUri)
  {
    $this->sourceNetworkUri = $sourceNetworkUri;
  }
  /**
   * @return string
   */
  public function getSourceNetworkUri()
  {
    return $this->sourceNetworkUri;
  }
  /**
   * Source port. Only valid when protocol is TCP or UDP.
   *
   * @param int $sourcePort
   */
  public function setSourcePort($sourcePort)
  {
    $this->sourcePort = $sourcePort;
  }
  /**
   * @return int
   */
  public function getSourcePort()
  {
    return $this->sourcePort;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EndpointInfo::class, 'Google_Service_NetworkManagement_EndpointInfo');
