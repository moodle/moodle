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

class GoogleChromeManagementV1DeviceActivityReport extends \Google\Model
{
  /**
   * Device activity state is unspecified.
   */
  public const DEVICE_ACTIVITY_STATE_DEVICE_ACTIVITY_STATE_UNSPECIFIED = 'DEVICE_ACTIVITY_STATE_UNSPECIFIED';
  /**
   * Device is currently being used.
   */
  public const DEVICE_ACTIVITY_STATE_ACTIVE = 'ACTIVE';
  /**
   * Device is currently idle.
   */
  public const DEVICE_ACTIVITY_STATE_IDLE = 'IDLE';
  /**
   * Device is currently locked.
   */
  public const DEVICE_ACTIVITY_STATE_LOCKED = 'LOCKED';
  /**
   * Output only. Device activity state.
   *
   * @var string
   */
  public $deviceActivityState;
  /**
   * Output only. Timestamp of when the report was collected.
   *
   * @var string
   */
  public $reportTime;

  /**
   * Output only. Device activity state.
   *
   * Accepted values: DEVICE_ACTIVITY_STATE_UNSPECIFIED, ACTIVE, IDLE, LOCKED
   *
   * @param self::DEVICE_ACTIVITY_STATE_* $deviceActivityState
   */
  public function setDeviceActivityState($deviceActivityState)
  {
    $this->deviceActivityState = $deviceActivityState;
  }
  /**
   * @return self::DEVICE_ACTIVITY_STATE_*
   */
  public function getDeviceActivityState()
  {
    return $this->deviceActivityState;
  }
  /**
   * Output only. Timestamp of when the report was collected.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1DeviceActivityReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1DeviceActivityReport');
