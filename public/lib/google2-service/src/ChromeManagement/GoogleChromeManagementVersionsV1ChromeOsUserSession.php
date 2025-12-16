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

class GoogleChromeManagementVersionsV1ChromeOsUserSession extends \Google\Model
{
  protected $chromeOsDeviceType = GoogleChromeManagementVersionsV1ChromeOsDevice::class;
  protected $chromeOsDeviceDataType = '';
  /**
   * Output only. The unique Directory API ID of the user.
   *
   * @var string
   */
  public $userDirectoryApiId;
  /**
   * Output only. The primary e-mail address of the user.
   *
   * @var string
   */
  public $userPrimaryEmail;

  /**
   * Output only. This field contains information about the ChromeOS device that
   * the user session is running on. It is only set if the user is affiliated,
   * i.e., if the user is managed by the same organization that manages the
   * ChromeOS device.
   *
   * @param GoogleChromeManagementVersionsV1ChromeOsDevice $chromeOsDevice
   */
  public function setChromeOsDevice(GoogleChromeManagementVersionsV1ChromeOsDevice $chromeOsDevice)
  {
    $this->chromeOsDevice = $chromeOsDevice;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ChromeOsDevice
   */
  public function getChromeOsDevice()
  {
    return $this->chromeOsDevice;
  }
  /**
   * Output only. The unique Directory API ID of the user.
   *
   * @param string $userDirectoryApiId
   */
  public function setUserDirectoryApiId($userDirectoryApiId)
  {
    $this->userDirectoryApiId = $userDirectoryApiId;
  }
  /**
   * @return string
   */
  public function getUserDirectoryApiId()
  {
    return $this->userDirectoryApiId;
  }
  /**
   * Output only. The primary e-mail address of the user.
   *
   * @param string $userPrimaryEmail
   */
  public function setUserPrimaryEmail($userPrimaryEmail)
  {
    $this->userPrimaryEmail = $userPrimaryEmail;
  }
  /**
   * @return string
   */
  public function getUserPrimaryEmail()
  {
    return $this->userPrimaryEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1ChromeOsUserSession::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1ChromeOsUserSession');
