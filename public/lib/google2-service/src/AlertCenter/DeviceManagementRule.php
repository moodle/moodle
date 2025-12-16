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

class DeviceManagementRule extends \Google\Model
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
   * The type of the device.
   *
   * @var string
   */
  public $deviceType;
  /**
   * The email of the user this alert was created for.
   *
   * @var string
   */
  public $email;
  /**
   * ID of the rule that triggered the alert
   *
   * @var string
   */
  public $id;
  /**
   * Required for iOS, empty for others.
   *
   * @var string
   */
  public $iosVendorId;
  /**
   * Obfuscated ID of the owner of the device
   *
   * @var string
   */
  public $ownerId;
  /**
   * The device resource ID.
   *
   * @var string
   */
  public $resourceId;
  /**
   * Action taken as result of the rule
   *
   * @var string
   */
  public $ruleAction;
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
   * The email of the user this alert was created for.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * ID of the rule that triggered the alert
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
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
   * Obfuscated ID of the owner of the device
   *
   * @param string $ownerId
   */
  public function setOwnerId($ownerId)
  {
    $this->ownerId = $ownerId;
  }
  /**
   * @return string
   */
  public function getOwnerId()
  {
    return $this->ownerId;
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
   * Action taken as result of the rule
   *
   * @param string $ruleAction
   */
  public function setRuleAction($ruleAction)
  {
    $this->ruleAction = $ruleAction;
  }
  /**
   * @return string
   */
  public function getRuleAction()
  {
    return $this->ruleAction;
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
class_alias(DeviceManagementRule::class, 'Google_Service_AlertCenter_DeviceManagementRule');
