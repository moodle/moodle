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

namespace Google\Service\AndroidManagement;

class PersonalUsagePolicies extends \Google\Collection
{
  /**
   * Unspecified. Defaults to BLUETOOTH_SHARING_ALLOWED.
   */
  public const BLUETOOTH_SHARING_BLUETOOTH_SHARING_UNSPECIFIED = 'BLUETOOTH_SHARING_UNSPECIFIED';
  /**
   * Bluetooth sharing is allowed on personal profile.Supported on Android 8 and
   * above. A NonComplianceDetail with MANAGEMENT_MODE is reported if this is
   * set for a personal device.
   */
  public const BLUETOOTH_SHARING_BLUETOOTH_SHARING_ALLOWED = 'BLUETOOTH_SHARING_ALLOWED';
  /**
   * Bluetooth sharing is disallowed on personal profile.Supported on Android 8
   * and above. A NonComplianceDetail with API_LEVEL is reported if the Android
   * version is less than 8. A NonComplianceDetail with MANAGEMENT_MODE is
   * reported if this is set for a personal device.
   */
  public const BLUETOOTH_SHARING_BLUETOOTH_SHARING_DISALLOWED = 'BLUETOOTH_SHARING_DISALLOWED';
  /**
   * Unspecified. Defaults to BLOCKLIST.
   */
  public const PERSONAL_PLAY_STORE_MODE_PLAY_STORE_MODE_UNSPECIFIED = 'PLAY_STORE_MODE_UNSPECIFIED';
  /**
   * All Play Store apps are available for installation in the personal profile,
   * except those whose installType is BLOCKED in personalApplications.
   *
   * @deprecated
   */
  public const PERSONAL_PLAY_STORE_MODE_BLACKLIST = 'BLACKLIST';
  /**
   * All Play Store apps are available for installation in the personal profile,
   * except those whose installType is BLOCKED in personalApplications.
   */
  public const PERSONAL_PLAY_STORE_MODE_BLOCKLIST = 'BLOCKLIST';
  /**
   * Only apps explicitly specified in personalApplications with installType set
   * to AVAILABLE are allowed to be installed in the personal profile.
   */
  public const PERSONAL_PLAY_STORE_MODE_ALLOWLIST = 'ALLOWLIST';
  /**
   * Unspecified. Defaults to PRIVATE_SPACE_ALLOWED.
   */
  public const PRIVATE_SPACE_POLICY_PRIVATE_SPACE_POLICY_UNSPECIFIED = 'PRIVATE_SPACE_POLICY_UNSPECIFIED';
  /**
   * Users can create a private space profile.
   */
  public const PRIVATE_SPACE_POLICY_PRIVATE_SPACE_ALLOWED = 'PRIVATE_SPACE_ALLOWED';
  /**
   * Users cannot create a private space profile. Supported only for company-
   * owned devices with a work profile. Caution: Any existing private space will
   * be removed.
   */
  public const PRIVATE_SPACE_POLICY_PRIVATE_SPACE_DISALLOWED = 'PRIVATE_SPACE_DISALLOWED';
  protected $collection_key = 'personalApplications';
  /**
   * Account types that can't be managed by the user.
   *
   * @var string[]
   */
  public $accountTypesWithManagementDisabled;
  /**
   * Optional. Whether bluetooth sharing is allowed.
   *
   * @var string
   */
  public $bluetoothSharing;
  /**
   * If true, the camera is disabled on the personal profile.
   *
   * @var bool
   */
  public $cameraDisabled;
  /**
   * Controls how long the work profile can stay off. The minimum duration must
   * be at least 3 days. Other details are as follows: - If the duration is set
   * to 0, the feature is turned off. - If the duration is set to a value
   * smaller than the minimum duration, the feature returns an error. *Note:* If
   * you want to avoid personal profiles being suspended during long periods of
   * off-time, you can temporarily set a large value for this parameter.
   *
   * @var int
   */
  public $maxDaysWithWorkOff;
  protected $personalApplicationsType = PersonalApplicationPolicy::class;
  protected $personalApplicationsDataType = 'array';
  /**
   * Used together with personalApplications to control how apps in the personal
   * profile are allowed or blocked.
   *
   * @var string
   */
  public $personalPlayStoreMode;
  /**
   * Optional. Controls whether a private space is allowed on the device.
   *
   * @var string
   */
  public $privateSpacePolicy;
  /**
   * If true, screen capture is disabled for all users.
   *
   * @var bool
   */
  public $screenCaptureDisabled;

  /**
   * Account types that can't be managed by the user.
   *
   * @param string[] $accountTypesWithManagementDisabled
   */
  public function setAccountTypesWithManagementDisabled($accountTypesWithManagementDisabled)
  {
    $this->accountTypesWithManagementDisabled = $accountTypesWithManagementDisabled;
  }
  /**
   * @return string[]
   */
  public function getAccountTypesWithManagementDisabled()
  {
    return $this->accountTypesWithManagementDisabled;
  }
  /**
   * Optional. Whether bluetooth sharing is allowed.
   *
   * Accepted values: BLUETOOTH_SHARING_UNSPECIFIED, BLUETOOTH_SHARING_ALLOWED,
   * BLUETOOTH_SHARING_DISALLOWED
   *
   * @param self::BLUETOOTH_SHARING_* $bluetoothSharing
   */
  public function setBluetoothSharing($bluetoothSharing)
  {
    $this->bluetoothSharing = $bluetoothSharing;
  }
  /**
   * @return self::BLUETOOTH_SHARING_*
   */
  public function getBluetoothSharing()
  {
    return $this->bluetoothSharing;
  }
  /**
   * If true, the camera is disabled on the personal profile.
   *
   * @param bool $cameraDisabled
   */
  public function setCameraDisabled($cameraDisabled)
  {
    $this->cameraDisabled = $cameraDisabled;
  }
  /**
   * @return bool
   */
  public function getCameraDisabled()
  {
    return $this->cameraDisabled;
  }
  /**
   * Controls how long the work profile can stay off. The minimum duration must
   * be at least 3 days. Other details are as follows: - If the duration is set
   * to 0, the feature is turned off. - If the duration is set to a value
   * smaller than the minimum duration, the feature returns an error. *Note:* If
   * you want to avoid personal profiles being suspended during long periods of
   * off-time, you can temporarily set a large value for this parameter.
   *
   * @param int $maxDaysWithWorkOff
   */
  public function setMaxDaysWithWorkOff($maxDaysWithWorkOff)
  {
    $this->maxDaysWithWorkOff = $maxDaysWithWorkOff;
  }
  /**
   * @return int
   */
  public function getMaxDaysWithWorkOff()
  {
    return $this->maxDaysWithWorkOff;
  }
  /**
   * Policy applied to applications in the personal profile.
   *
   * @param PersonalApplicationPolicy[] $personalApplications
   */
  public function setPersonalApplications($personalApplications)
  {
    $this->personalApplications = $personalApplications;
  }
  /**
   * @return PersonalApplicationPolicy[]
   */
  public function getPersonalApplications()
  {
    return $this->personalApplications;
  }
  /**
   * Used together with personalApplications to control how apps in the personal
   * profile are allowed or blocked.
   *
   * Accepted values: PLAY_STORE_MODE_UNSPECIFIED, BLACKLIST, BLOCKLIST,
   * ALLOWLIST
   *
   * @param self::PERSONAL_PLAY_STORE_MODE_* $personalPlayStoreMode
   */
  public function setPersonalPlayStoreMode($personalPlayStoreMode)
  {
    $this->personalPlayStoreMode = $personalPlayStoreMode;
  }
  /**
   * @return self::PERSONAL_PLAY_STORE_MODE_*
   */
  public function getPersonalPlayStoreMode()
  {
    return $this->personalPlayStoreMode;
  }
  /**
   * Optional. Controls whether a private space is allowed on the device.
   *
   * Accepted values: PRIVATE_SPACE_POLICY_UNSPECIFIED, PRIVATE_SPACE_ALLOWED,
   * PRIVATE_SPACE_DISALLOWED
   *
   * @param self::PRIVATE_SPACE_POLICY_* $privateSpacePolicy
   */
  public function setPrivateSpacePolicy($privateSpacePolicy)
  {
    $this->privateSpacePolicy = $privateSpacePolicy;
  }
  /**
   * @return self::PRIVATE_SPACE_POLICY_*
   */
  public function getPrivateSpacePolicy()
  {
    return $this->privateSpacePolicy;
  }
  /**
   * If true, screen capture is disabled for all users.
   *
   * @param bool $screenCaptureDisabled
   */
  public function setScreenCaptureDisabled($screenCaptureDisabled)
  {
    $this->screenCaptureDisabled = $screenCaptureDisabled;
  }
  /**
   * @return bool
   */
  public function getScreenCaptureDisabled()
  {
    return $this->screenCaptureDisabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PersonalUsagePolicies::class, 'Google_Service_AndroidManagement_PersonalUsagePolicies');
