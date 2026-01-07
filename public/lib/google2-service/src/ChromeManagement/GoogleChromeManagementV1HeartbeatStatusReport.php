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

class GoogleChromeManagementV1HeartbeatStatusReport extends \Google\Model
{
  /**
   * State not specified
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Device is not eligible for heartbeat monitoring
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * Device is online
   */
  public const STATE_ONLINE = 'ONLINE';
  /**
   * Device is offline
   */
  public const STATE_OFFLINE = 'OFFLINE';
  /**
   * Device is outdated
   */
  public const STATE_DEVICE_OUTDATED = 'DEVICE_OUTDATED';
  /**
   * Timestamp of when status changed was detected
   *
   * @var string
   */
  public $reportTime;
  /**
   * State the device changed to
   *
   * @var string
   */
  public $state;

  /**
   * Timestamp of when status changed was detected
   *
   * @param string $reportTime
   */
  public function setReportTime($reportTime)
  {
    $this->reportTime = $reportTime;
  }
  /**
   * @return string
   */
  public function getReportTime()
  {
    return $this->reportTime;
  }
  /**
   * State the device changed to
   *
   * Accepted values: STATE_UNSPECIFIED, UNKNOWN, ONLINE, OFFLINE,
   * DEVICE_OUTDATED
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
class_alias(GoogleChromeManagementV1HeartbeatStatusReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1HeartbeatStatusReport');
