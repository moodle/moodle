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

namespace Google\Service\AndroidEnterprise;

class DeviceReportUpdateEvent extends \Google\Model
{
  /**
   * The Android ID of the device. This field will always be present.
   *
   * @var string
   */
  public $deviceId;
  protected $reportType = DeviceReport::class;
  protected $reportDataType = '';
  /**
   * The ID of the user. This field will always be present.
   *
   * @var string
   */
  public $userId;

  /**
   * The Android ID of the device. This field will always be present.
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
   * The device report updated with the latest app states. This field will
   * always be present.
   *
   * @param DeviceReport $report
   */
  public function setReport(DeviceReport $report)
  {
    $this->report = $report;
  }
  /**
   * @return DeviceReport
   */
  public function getReport()
  {
    return $this->report;
  }
  /**
   * The ID of the user. This field will always be present.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceReportUpdateEvent::class, 'Google_Service_AndroidEnterprise_DeviceReportUpdateEvent');
