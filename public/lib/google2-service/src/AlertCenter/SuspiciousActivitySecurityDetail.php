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

class SuspiciousActivitySecurityDetail extends \Google\Model
{
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
   * The device property which was changed.
   *
   * @var string
   */
  public $deviceProperty;
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
   * The new value of the device property after the change.
   *
   * @var string
   */
  public $newValue;
  /**
   * The old value of the device property before the change.
   *
   * @var string
   */
  public $oldValue;
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
   * The device property which was changed.
   *
   * @param string $deviceProperty
   */
  public function setDeviceProperty($deviceProperty)
  {
    $this->deviceProperty = $deviceProperty;
  }
  /**
   * @return string
   */
  public function getDeviceProperty()
  {
    return $this->deviceProperty;
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
   * The new value of the device property after the change.
   *
   * @param string $newValue
   */
  public function setNewValue($newValue)
  {
    $this->newValue = $newValue;
  }
  /**
   * @return string
   */
  public function getNewValue()
  {
    return $this->newValue;
  }
  /**
   * The old value of the device property before the change.
   *
   * @param string $oldValue
   */
  public function setOldValue($oldValue)
  {
    $this->oldValue = $oldValue;
  }
  /**
   * @return string
   */
  public function getOldValue()
  {
    return $this->oldValue;
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
class_alias(SuspiciousActivitySecurityDetail::class, 'Google_Service_AlertCenter_SuspiciousActivitySecurityDetail');
