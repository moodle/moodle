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

class GoogleChromeManagementV1TelemetryNotificationFilter extends \Google\Model
{
  /**
   * If set, only sends notifications for telemetry data coming from this
   * device.
   *
   * @var string
   */
  public $deviceId;
  /**
   * If set, only sends notifications for telemetry data coming from devices in
   * this org unit.
   *
   * @var string
   */
  public $deviceOrgUnitId;
  protected $telemetryEventNotificationFilterType = GoogleChromeManagementV1TelemetryEventNotificationFilter::class;
  protected $telemetryEventNotificationFilterDataType = '';
  /**
   * If set, only sends notifications for telemetry data coming from devices
   * owned by this user.
   *
   * @var string
   */
  public $userEmail;
  /**
   * If set, only sends notifications for telemetry data coming from devices
   * owned by users in this org unit.
   *
   * @var string
   */
  public $userOrgUnitId;

  /**
   * If set, only sends notifications for telemetry data coming from this
   * device.
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
   * If set, only sends notifications for telemetry data coming from devices in
   * this org unit.
   *
   * @param string $deviceOrgUnitId
   */
  public function setDeviceOrgUnitId($deviceOrgUnitId)
  {
    $this->deviceOrgUnitId = $deviceOrgUnitId;
  }
  /**
   * @return string
   */
  public function getDeviceOrgUnitId()
  {
    return $this->deviceOrgUnitId;
  }
  /**
   * Only sends notifications for the telemetry events matching this filter.
   *
   * @param GoogleChromeManagementV1TelemetryEventNotificationFilter $telemetryEventNotificationFilter
   */
  public function setTelemetryEventNotificationFilter(GoogleChromeManagementV1TelemetryEventNotificationFilter $telemetryEventNotificationFilter)
  {
    $this->telemetryEventNotificationFilter = $telemetryEventNotificationFilter;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryEventNotificationFilter
   */
  public function getTelemetryEventNotificationFilter()
  {
    return $this->telemetryEventNotificationFilter;
  }
  /**
   * If set, only sends notifications for telemetry data coming from devices
   * owned by this user.
   *
   * @param string $userEmail
   */
  public function setUserEmail($userEmail)
  {
    $this->userEmail = $userEmail;
  }
  /**
   * @return string
   */
  public function getUserEmail()
  {
    return $this->userEmail;
  }
  /**
   * If set, only sends notifications for telemetry data coming from devices
   * owned by users in this org unit.
   *
   * @param string $userOrgUnitId
   */
  public function setUserOrgUnitId($userOrgUnitId)
  {
    $this->userOrgUnitId = $userOrgUnitId;
  }
  /**
   * @return string
   */
  public function getUserOrgUnitId()
  {
    return $this->userOrgUnitId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TelemetryNotificationFilter::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryNotificationFilter');
