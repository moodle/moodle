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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2Connection extends \Google\Model
{
  /**
   * Unspecified protocol (not HOPOPT).
   */
  public const PROTOCOL_PROTOCOL_UNSPECIFIED = 'PROTOCOL_UNSPECIFIED';
  /**
   * Internet Control Message Protocol.
   */
  public const PROTOCOL_ICMP = 'ICMP';
  /**
   * Transmission Control Protocol.
   */
  public const PROTOCOL_TCP = 'TCP';
  /**
   * User Datagram Protocol.
   */
  public const PROTOCOL_UDP = 'UDP';
  /**
   * Generic Routing Encapsulation.
   */
  public const PROTOCOL_GRE = 'GRE';
  /**
   * Encap Security Payload.
   */
  public const PROTOCOL_ESP = 'ESP';
  /**
   * Destination IP address. Not present for sockets that are listening and not
   * connected.
   *
   * @var string
   */
  public $destinationIp;
  /**
   * Destination port. Not present for sockets that are listening and not
   * connected.
   *
   * @var int
   */
  public $destinationPort;
  /**
   * IANA Internet Protocol Number such as TCP(6) and UDP(17).
   *
   * @var string
   */
  public $protocol;
  /**
   * Source IP address.
   *
   * @var string
   */
  public $sourceIp;
  /**
   * Source port.
   *
   * @var int
   */
  public $sourcePort;

  /**
   * Destination IP address. Not present for sockets that are listening and not
   * connected.
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
   * Destination port. Not present for sockets that are listening and not
   * connected.
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
   * IANA Internet Protocol Number such as TCP(6) and UDP(17).
   *
   * Accepted values: PROTOCOL_UNSPECIFIED, ICMP, TCP, UDP, GRE, ESP
   *
   * @param self::PROTOCOL_* $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return self::PROTOCOL_*
   */
  public function getProtocol()
  {
    return $this->protocol;
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
   * Source port.
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
class_alias(GoogleCloudSecuritycenterV2Connection::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2Connection');
