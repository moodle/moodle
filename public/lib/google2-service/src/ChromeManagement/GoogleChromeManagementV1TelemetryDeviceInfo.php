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

class GoogleChromeManagementV1TelemetryDeviceInfo extends \Google\Model
{
  /**
   * Output only. The unique Directory API ID of the device. This value is the
   * same as the Admin Console's Directory API ID in the ChromeOS Devices tab.
   *
   * @var string
   */
  public $deviceId;
  /**
   * Output only. Organization unit ID of the device.
   *
   * @var string
   */
  public $orgUnitId;

  /**
   * Output only. The unique Directory API ID of the device. This value is the
   * same as the Admin Console's Directory API ID in the ChromeOS Devices tab.
   *
   * @param string $deviceId
   */
  public function setDeviceId($deviceId)
  {
    $this->deviceId = $deviceId;
  }
  /**
   * @return string
   */
  public function getDeviceId()
  {
    return $this->deviceId;
  }
  /**
   * Output only. Organization unit ID of the device.
   *
   * @param string $orgUnitId
   */
  public function setOrgUnitId($orgUnitId)
  {
    $this->orgUnitId = $orgUnitId;
  }
  /**
   * @return string
   */
  public function getOrgUnitId()
  {
    return $this->orgUnitId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryDeviceInfo::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryDeviceInfo');
