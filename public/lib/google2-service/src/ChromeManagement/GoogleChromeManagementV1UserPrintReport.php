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

class GoogleChromeManagementV1UserPrintReport extends \Google\Model
{
  /**
   * Number of chrome devices that have been used to initiate print jobs by the
   * user.
   *
   * @var string
   */
  public $deviceCount;
  /**
   * Number of print jobs initiated by the user.
   *
   * @var string
   */
  public $jobCount;
  /**
   * Number of printers used by the user.
   *
   * @var string
   */
  public $printerCount;
  /**
   * The primary e-mail address of the user.
   *
   * @var string
   */
  public $userEmail;
  /**
   * The unique Directory API ID of the user.
   *
   * @var string
   */
  public $userId;

  /**
   * Number of chrome devices that have been used to initiate print jobs by the
   * user.
   *
   * @param string $deviceCount
   */
  public function setDeviceCount($deviceCount)
  {
    $this->deviceCount = $deviceCount;
  }
  /**
   * @return string
   */
  public function getDeviceCount()
  {
    return $this->deviceCount;
  }
  /**
   * Number of print jobs initiated by the user.
   *
   * @param string $jobCount
   */
  public function setJobCount($jobCount)
  {
    $this->jobCount = $jobCount;
  }
  /**
   * @return string
   */
  public function getJobCount()
  {
    return $this->jobCount;
  }
  /**
   * Number of printers used by the user.
   *
   * @param string $printerCount
   */
  public function setPrinterCount($printerCount)
  {
    $this->printerCount = $printerCount;
  }
  /**
   * @return string
   */
  public function getPrinterCount()
  {
    return $this->printerCount;
  }
  /**
   * The primary e-mail address of the user.
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
   * The unique Directory API ID of the user.
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
class_alias(GoogleChromeManagementV1UserPrintReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1UserPrintReport');
