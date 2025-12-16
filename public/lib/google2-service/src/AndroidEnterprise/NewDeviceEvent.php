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

class NewDeviceEvent extends \Google\Model
{
  public const MANAGEMENT_TYPE_managedDevice = 'managedDevice';
  public const MANAGEMENT_TYPE_managedProfile = 'managedProfile';
  /**
   * The Android ID of the device. This field will always be present.
   *
   * @var string
   */
  public $deviceId;
  /**
   * Policy app on the device.
   *
   * @var string
   */
  public $dpcPackageName;
  /**
   * Identifies the extent to which the device is controlled by an Android EMM
   * in various deployment configurations. Possible values include: -
   * "managedDevice", a device where the DPC is set as device owner, -
   * "managedProfile", a device where the DPC is set as profile owner.
   *
   * @var string
   */
  public $managementType;
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
   * Policy app on the device.
   *
   * @param string $dpcPackageName
   */
  public function setDpcPackageName($dpcPackageName)
  {
    $this->dpcPackageName = $dpcPackageName;
  }
  /**
   * @return string
   */
  public function getDpcPackageName()
  {
    return $this->dpcPackageName;
  }
  /**
   * Identifies the extent to which the device is controlled by an Android EMM
   * in various deployment configurations. Possible values include: -
   * "managedDevice", a device where the DPC is set as device owner, -
   * "managedProfile", a device where the DPC is set as profile owner.
   *
   * Accepted values: managedDevice, managedProfile
   *
   * @param self::MANAGEMENT_TYPE_* $managementType
   */
  public function setManagementType($managementType)
  {
    $this->managementType = $managementType;
  }
  /**
   * @return self::MANAGEMENT_TYPE_*
   */
  public function getManagementType()
  {
    return $this->managementType;
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
class_alias(NewDeviceEvent::class, 'Google_Service_AndroidEnterprise_NewDeviceEvent');
