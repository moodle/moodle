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

namespace Google\Service\MigrationCenterAPI;

class NetworkConnection extends \Google\Model
{
  /**
   * Connection state is unknown or unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The connection is being opened.
   */
  public const STATE_OPENING = 'OPENING';
  /**
   * The connection is open.
   */
  public const STATE_OPEN = 'OPEN';
  /**
   * Listening for incoming connections.
   */
  public const STATE_LISTEN = 'LISTEN';
  /**
   * The connection is being closed.
   */
  public const STATE_CLOSING = 'CLOSING';
  /**
   * The connection is closed.
   */
  public const STATE_CLOSED = 'CLOSED';
  /**
   * Local IP address.
   *
   * @var string
   */
  public $localIpAddress;
  /**
   * Local port.
   *
   * @var int
   */
  public $localPort;
  /**
   * Process ID.
   *
   * @var string
   */
  public $pid;
  /**
   * Process or service name.
   *
   * @var string
   */
  public $processName;
  /**
   * Connection protocol (e.g. TCP/UDP).
   *
   * @var string
   */
  public $protocol;
  /**
   * Remote IP address.
   *
   * @var string
   */
  public $remoteIpAddress;
  /**
   * Remote port.
   *
   * @var int
   */
  public $remotePort;
  /**
   * Network connection state.
   *
   * @var string
   */
  public $state;

  /**
   * Local IP address.
   *
   * @param string $localIpAddress
   */
  public function setLocalIpAddress($localIpAddress)
  {
    $this->localIpAddress = $localIpAddress;
  }
  /**
   * @return string
   */
  public function getLocalIpAddress()
  {
    return $this->localIpAddress;
  }
  /**
   * Local port.
   *
   * @param int $localPort
   */
  public function setLocalPort($localPort)
  {
    $this->localPort = $localPort;
  }
  /**
   * @return int
   */
  public function getLocalPort()
  {
    return $this->localPort;
  }
  /**
   * Process ID.
   *
   * @param string $pid
   */
  public function setPid($pid)
  {
    $this->pid = $pid;
  }
  /**
   * @return string
   */
  public function getPid()
  {
    return $this->pid;
  }
  /**
   * Process or service name.
   *
   * @param string $processName
   */
  public function setProcessName($processName)
  {
    $this->processName = $processName;
  }
  /**
   * @return string
   */
  public function getProcessName()
  {
    return $this->processName;
  }
  /**
   * Connection protocol (e.g. TCP/UDP).
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
   * Remote IP address.
   *
   * @param string $remoteIpAddress
   */
  public function setRemoteIpAddress($remoteIpAddress)
  {
    $this->remoteIpAddress = $remoteIpAddress;
  }
  /**
   * @return string
   */
  public function getRemoteIpAddress()
  {
    return $this->remoteIpAddress;
  }
  /**
   * Remote port.
   *
   * @param int $remotePort
   */
  public function setRemotePort($remotePort)
  {
    $this->remotePort = $remotePort;
  }
  /**
   * @return int
   */
  public function getRemotePort()
  {
    return $this->remotePort;
  }
  /**
   * Network connection state.
   *
   * Accepted values: STATE_UNSPECIFIED, OPENING, OPEN, LISTEN, CLOSING, CLOSED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkConnection::class, 'Google_Service_MigrationCenterAPI_NetworkConnection');
