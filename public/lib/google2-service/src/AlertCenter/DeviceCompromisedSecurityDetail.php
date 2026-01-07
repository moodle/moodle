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

namespace Google\Service\AlertCenter;

class DeviceCompromisedSecurityDetail extends \Google\Model
{
  /**
   * The device compromised state. Possible values are "`Compromised`" or "`Not
   * Compromised`".
   *
   * @var string
   */
  public $deviceCompromisedState;
  /**
   * Required. The device ID.
   *
   * @var string
   */
  public $deviceId;
  /**
   * The model of the device.
   *
   * @var string
   */
  public $deviceModel;
  /**
   * The type of the device.
   *
   * @var string
   */
  public $deviceType;
  /**
   * Required for iOS, empty for others.
   *
   * @var string
   */
  public $iosVendorId;
  /**
   * The device resource ID.
   *
   * @var string
   */
  public $resourceId;
  /**
   * The serial number of the device.
   *
   * @var string
   */
  public $serialNumber;

  /**
   * The device compromised state. Possible values are "`Compromised`" or "`Not
   * Compromised`".
   *
   * @param string $deviceCompromisedState
   */
  public function setDeviceCompromisedState($deviceCompromisedState)
  {
    $this->deviceCompromisedState = $deviceCompromisedState;
  }
  /**
   * @return string
   */
  public function getDeviceCompromisedState()
  {
    return $this->deviceCompromisedState;
  }
  /**
   * Required. The device ID.
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
   * The model of the device.
   *
   * @param string $deviceModel
   */
  public function setDeviceModel($deviceModel)
  {
    $this->deviceModel = $deviceModel;
  }
  /**
   * @return string
   */
  public function getDeviceModel()
  {
    return $this->deviceModel;
  }
  /**
   * The type of the device.
   *
   * @param string $deviceType
   */
  public function setDeviceType($deviceType)
  {
    $this->deviceType = $deviceType;
  }
  /**
   * @return string
   */
  public function getDeviceType()
  {
    return $this->deviceType;
  }
  /**
   * Required for iOS, empty for others.
   *
   * @param string $iosVendorId
   */
  public function setIosVendorId($iosVendorId)
  {
    $this->iosVendorId = $iosVendorId;
  }
  /**
   * @return string
   */
  public function getIosVendorId()
  {
    return $this->iosVendorId;
  }
  /**
   * The device resource ID.
   *
   * @param string $resourceId
   */
  public function setResourceId($resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return string
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * The serial number of the device.
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
class_alias(DeviceCompromisedSecurityDetail::class, 'Google_Service_AlertCenter_DeviceCompromisedSecurityDetail');
