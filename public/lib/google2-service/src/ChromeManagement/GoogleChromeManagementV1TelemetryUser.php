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

class GoogleChromeManagementV1TelemetryUser extends \Google\Collection
{
  protected $collection_key = 'userDevice';
  /**
   * G Suite Customer whose enterprise enrolled the device.
   *
   * @var string
   */
  public $customer;
  /**
   * Resource name of the user.
   *
   * @var string
   */
  public $name;
  /**
   * Organization unit of the user.
   *
   * @var string
   */
  public $orgUnitId;
  protected $userDeviceType = GoogleChromeManagementV1TelemetryUserDevice::class;
  protected $userDeviceDataType = 'array';
  /**
   * Email address of the user.
   *
   * @var string
   */
  public $userEmail;
  /**
   * Directory ID of the user.
   *
   * @var string
   */
  public $userId;

  /**
   * G Suite Customer whose enterprise enrolled the device.
   *
   * @param string $customer
   */
  public function setCustomer($customer)
  {
    $this->customer = $customer;
  }
  /**
   * @return string
   */
  public function getCustomer()
  {
    return $this->customer;
  }
  /**
   * Resource name of the user.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Organization unit of the user.
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
  /**
   * Telemetry data collected from a managed user and device.
   *
   * @param GoogleChromeManagementV1TelemetryUserDevice[] $userDevice
   */
  public function setUserDevice($userDevice)
  {
    $this->userDevice = $userDevice;
  }
  /**
   * @return GoogleChromeManagementV1TelemetryUserDevice[]
   */
  public function getUserDevice()
  {
    return $this->userDevice;
  }
  /**
   * Email address of the user.
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
   * Directory ID of the user.
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
class_alias(GoogleChromeManagementV1TelemetryUser::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TelemetryUser');
