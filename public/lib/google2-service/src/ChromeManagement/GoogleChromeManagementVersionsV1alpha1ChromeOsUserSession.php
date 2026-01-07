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

class GoogleChromeManagementVersionsV1alpha1ChromeOsUserSession extends \Google\Model
{
  protected $chromeOsDeviceType = GoogleChromeManagementVersionsV1alpha1ChromeOsDevice::class;
  protected $chromeOsDeviceDataType = '';
  /**
   * @var string
   */
  public $userDirectoryApiId;
  /**
   * @var string
   */
  public $userPrimaryEmail;

  /**
   * @param GoogleChromeManagementVersionsV1alpha1ChromeOsDevice
   */
  public function setChromeOsDevice(GoogleChromeManagementVersionsV1alpha1ChromeOsDevice $chromeOsDevice)
  {
    $this->chromeOsDevice = $chromeOsDevice;
  }
  /**
   * @return GoogleChromeManagementVersionsV1alpha1ChromeOsDevice
   */
  public function getChromeOsDevice()
  {
    return $this->chromeOsDevice;
  }
  /**
   * @param string
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
   * @param string
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
class_alias(GoogleChromeManagementVersionsV1alpha1ChromeOsUserSession::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1alpha1ChromeOsUserSession');
