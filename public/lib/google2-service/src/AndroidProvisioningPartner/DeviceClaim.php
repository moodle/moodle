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

class DeviceClaim extends \Google\Model
{
  /**
   * No additional service.
   */
  public const ADDITIONAL_SERVICE_ADDITIONAL_SERVICE_UNSPECIFIED = 'ADDITIONAL_SERVICE_UNSPECIFIED';
  /**
   * Device protection service, also known as Android Enterprise Essentials. To
   * claim a device with the device protection service you must enroll with the
   * partnership team.
   */
  public const ADDITIONAL_SERVICE_DEVICE_PROTECTION = 'DEVICE_PROTECTION';
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
   * The Additional service registered for the device.
   *
   * @var string
   */
  public $additionalService;
  /**
   * The ID of the Google Workspace account that owns the Chrome OS device.
   *
   * @var string
   */
  public $googleWorkspaceCustomerId;
  /**
   * The ID of the Customer that purchased the device.
   *
   * @var string
   */
  public $ownerCompanyId;
  /**
   * The ID of the reseller that claimed the device.
   *
   * @var string
   */
  public $resellerId;
  /**
   * Output only. The type of claim made on the device.
   *
   * @var string
   */
  public $sectionType;
  /**
   * The timestamp when the device will exit ‘vacation mode’. This value is
   * present iff the device is in 'vacation mode'.
   *
   * @var string
   */
  public $vacationModeExpireTime;
  /**
   * The timestamp when the device was put into ‘vacation mode’. This value is
   * present iff the device is in 'vacation mode'.
   *
   * @var string
   */
  public $vacationModeStartTime;

  /**
   * The Additional service registered for the device.
   *
   * Accepted values: ADDITIONAL_SERVICE_UNSPECIFIED, DEVICE_PROTECTION
   *
   * @param self::ADDITIONAL_SERVICE_* $additionalService
   */
  public function setAdditionalService($additionalService)
  {
    $this->additionalService = $additionalService;
  }
  /**
   * @return self::ADDITIONAL_SERVICE_*
   */
  public function getAdditionalService()
  {
    return $this->additionalService;
  }
  /**
   * The ID of the Google Workspace account that owns the Chrome OS device.
   *
   * @param string $googleWorkspaceCustomerId
   */
  public function setGoogleWorkspaceCustomerId($googleWorkspaceCustomerId)
  {
    $this->googleWorkspaceCustomerId = $googleWorkspaceCustomerId;
  }
  /**
   * @return string
   */
  public function getGoogleWorkspaceCustomerId()
  {
    return $this->googleWorkspaceCustomerId;
  }
  /**
   * The ID of the Customer that purchased the device.
   *
   * @param string $ownerCompanyId
   */
  public function setOwnerCompanyId($ownerCompanyId)
  {
    $this->ownerCompanyId = $ownerCompanyId;
  }
  /**
   * @return string
   */
  public function getOwnerCompanyId()
  {
    return $this->ownerCompanyId;
  }
  /**
   * The ID of the reseller that claimed the device.
   *
   * @param string $resellerId
   */
  public function setResellerId($resellerId)
  {
    $this->resellerId = $resellerId;
  }
  /**
   * @return string
   */
  public function getResellerId()
  {
    return $this->resellerId;
  }
  /**
   * Output only. The type of claim made on the device.
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
   * The timestamp when the device will exit ‘vacation mode’. This value is
   * present iff the device is in 'vacation mode'.
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
  /**
   * The timestamp when the device was put into ‘vacation mode’. This value is
   * present iff the device is in 'vacation mode'.
   *
   * @param string $vacationModeStartTime
   */
  public function setVacationModeStartTime($vacationModeStartTime)
  {
    $this->vacationModeStartTime = $vacationModeStartTime;
  }
  /**
   * @return string
   */
  public function getVacationModeStartTime()
  {
    return $this->vacationModeStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceClaim::class, 'Google_Service_AndroidProvisioningPartner_DeviceClaim');
