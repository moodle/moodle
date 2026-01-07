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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1TelemetryNetworkConnectionStateChangeEvent extends \Google\Model
{
  /**
   * Network connection state unspecified.
   */
  public const CONNECTION_STATE_NETWORK_CONNECTION_STATE_UNSPECIFIED = 'NETWORK_CONNECTION_STATE_UNSPECIFIED';
  /**
   * The network is connected and internet connectivity is available.
   */
  public const CONNECTION_STATE_ONLINE = 'ONLINE';
  /**
   * The network is connected and not in a detected portal state, but internet
   * connectivity may not be available.
   */
  public const CONNECTION_STATE_CONNECTED = 'CONNECTED';
  /**
   * The network is connected but a portal state was detected. Internet
   * connectivity may be limited.
   */
  public const CONNECTION_STATE_PORTAL = 'PORTAL';
  /**
   * The network is in the process of connecting.
   */
  public const CONNECTION_STATE_CONNECTING = 'CONNECTING';
  /**
   * The network is not connected.
   */
  public const CONNECTION_STATE_NOT_CONNECTED = 'NOT_CONNECTED';
  /**
   * Current connection state of the network.
   *
   * @var string
   */
  public $connectionState;
  /**
   * Unique identifier of the network.
   *
   * @var string
   */
  public $guid;

  /**
   * Current connection state of the network.
   *
   * Accepted values: NETWORK_CONNECTION_STATE_UNSPECIFIED, ONLINE, CONNECTED,
   * PORTAL, CONNECTING, NOT_CONNECTED
   *
   * @param self::CONNECTION_STATE_* $connectionState
   */
  public function setConnectionState($connectionState)
  {
    $this->connectionState = $connectionState;
  }
  /**
   * @return self::CONNECTION_STATE_*
   */
  public function getConnectionState()
  {
    return $this->connectionState;
  }
  /**
   * Unique identifier of the network.
   *
   * @param string $guid
   */
  public function setGuid($guid)
  {
    $this->guid = $guid;
  }
  /**
   * @return string
   */
  public function getGuid()
  {
    return $this->guid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryNetworkConnectionStateChangeEvent::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryNetworkConnectionStateChangeEvent');
