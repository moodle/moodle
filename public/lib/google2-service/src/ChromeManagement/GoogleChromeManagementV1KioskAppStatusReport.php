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

class GoogleChromeManagementV1KioskAppStatusReport extends \Google\Model
{
  /**
   * App id of kiosk app for example "mdmkkicfmmkgmpkmkdikhlbggogpicma"
   *
   * @var string
   */
  public $appId;
  /**
   * App version number of kiosk app for example "1.10.118"
   *
   * @var string
   */
  public $appVersion;
  /**
   * Timestamp of when report was collected
   *
   * @var string
   */
  public $reportTime;

  /**
   * App id of kiosk app for example "mdmkkicfmmkgmpkmkdikhlbggogpicma"
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * App version number of kiosk app for example "1.10.118"
   *
   * @param string $appVersion
   */
  public function setAppVersion($appVersion)
  {
    $this->appVersion = $appVersion;
  }
  /**
   * @return string
   */
  public function getAppVersion()
  {
    return $this->appVersion;
  }
  /**
   * Timestamp of when report was collected
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
class_alias(GoogleChromeManagementV1KioskAppStatusReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1KioskAppStatusReport');
