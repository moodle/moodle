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

namespace Google\Service\AndroidProvisioningPartner;

class UnclaimDeviceRequest extends \Google\Model
{
  /**
   * Unspecified section type.
   */
  public const SECTION_TYPE_SECTION_TYPE_UNSPECIFIED = 'SECTION_TYPE_UNSPECIFIED';
  /**
   * SIM-lock section type.
   */
  public const SECTION_TYPE_SECTION_TYPE_SIM_LOCK = 'SECTION_TYPE_SIM_LOCK';
  /**
   * Zero-touch enrollment section type.
   */
  public const SECTION_TYPE_SECTION_TYPE_ZERO_TOUCH = 'SECTION_TYPE_ZERO_TOUCH';
  /**
   * Required. The device ID returned by `ClaimDevice`.
   *
   * @var string
   */
  public $deviceId;
  protected $deviceIdentifierType = DeviceIdentifier::class;
  protected $deviceIdentifierDataType = '';
  /**
   * Required. The section type of the device's provisioning record.
   *
   * @var string
   */
  public $sectionType;
  /**
   * The duration of the vacation unlock starting from when the request is
   * processed. (1 day is treated as 24 hours)
   *
   * @var int
   */
  public $vacationModeDays;
  /**
   * The expiration time of the vacation unlock.
   *
   * @var string
   */
  public $vacationModeExpireTime;

  /**
   * Required. The device ID returned by `ClaimDevice`.
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
   * Required. The device identifier you used when you claimed this device.
   *
   * @param DeviceIdentifier $deviceIdentifier
   */
  public function setDeviceIdentifier(DeviceIdentifier $deviceIdentifier)
  {
    $this->deviceIdentifier = $deviceIdentifier;
  }
  /**
   * @return DeviceIdentifier
   */
  public function getDeviceIdentifier()
  {
    return $this->deviceIdentifier;
  }
  /**
   * Required. The section type of the device's provisioning record.
   *
   * Accepted values: SECTION_TYPE_UNSPECIFIED, SECTION_TYPE_SIM_LOCK,
   * SECTION_TYPE_ZERO_TOUCH
   *
   * @param self::SECTION_TYPE_* $sectionType
   */
  public function setSectionType($sectionType)
  {
    $this->sectionType = $sectionType;
  }
  /**
   * @return self::SECTION_TYPE_*
   */
  public function getSectionType()
  {
    return $this->sectionType;
  }
  /**
   * The duration of the vacation unlock starting from when the request is
   * processed. (1 day is treated as 24 hours)
   *
   * @param int $vacationModeDays
   */
  public function setVacationModeDays($vacationModeDays)
  {
    $this->vacationModeDays = $vacationModeDays;
  }
  /**
   * @return int
   */
  public function getVacationModeDays()
  {
    return $this->vacationModeDays;
  }
  /**
   * The expiration time of the vacation unlock.
   *
   * @param string $vacationModeExpireTime
   */
  public function setVacationModeExpireTime($vacationModeExpireTime)
  {
    $this->vacationModeExpireTime = $vacationModeExpireTime;
  }
  /**
   * @return string
   */
  public function getVacationModeExpireTime()
  {
    return $this->vacationModeExpireTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UnclaimDeviceRequest::class, 'Google_Service_AndroidProvisioningPartner_UnclaimDeviceRequest');
