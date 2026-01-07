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

class GoogleChromeManagementV1TelemetryNetworkSignalStrengthEvent extends \Google\Model
{
  /**
   * Unique identifier of the network.
   *
   * @var string
   */
  public $guid;
  /**
   * Signal strength RSSI value.
   *
   * @var int
   */
  public $signalStrengthDbm;

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
  /**
   * Signal strength RSSI value.
   *
   * @param int $signalStrengthDbm
   */
  public function setSignalStrengthDbm($signalStrengthDbm)
  {
    $this->signalStrengthDbm = $signalStrengthDbm;
  }
  /**
   * @return int
   */
  public function getSignalStrengthDbm()
  {
    return $this->signalStrengthDbm;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryNetworkSignalStrengthEvent::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryNetworkSignalStrengthEvent');
