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

class GoogleChromeManagementVersionsV1ChromeOsDevice extends \Google\Model
{
  /**
   * Output only. The unique Directory API ID of the device. This value is the
   * same as the Admin Console's Directory API ID in the ChromeOS Devices tab.
   *
   * @var string
   */
  public $deviceDirectoryApiId;
  /**
   * Output only. Device serial number. This value is the same as the Admin
   * Console's Serial Number in the ChromeOS Devices tab.
   *
   * @var string
   */
  public $serialNumber;

  /**
   * Output only. The unique Directory API ID of the device. This value is the
   * same as the Admin Console's Directory API ID in the ChromeOS Devices tab.
   *
   * @param string $deviceDirectoryApiId
   */
  public function setDeviceDirectoryApiId($deviceDirectoryApiId)
  {
    $this->deviceDirectoryApiId = $deviceDirectoryApiId;
  }
  /**
   * @return string
   */
  public function getDeviceDirectoryApiId()
  {
    return $this->deviceDirectoryApiId;
  }
  /**
   * Output only. Device serial number. This value is the same as the Admin
   * Console's Serial Number in the ChromeOS Devices tab.
   *
   * @param string $serialNumber
   */
  public function setSerialNumber($serialNumber)
  {
    $this->serialNumber = $serialNumber;
  }
  /**
   * @return string
   */
  public function getSerialNumber()
  {
    return $this->serialNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1ChromeOsDevice::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1ChromeOsDevice');
