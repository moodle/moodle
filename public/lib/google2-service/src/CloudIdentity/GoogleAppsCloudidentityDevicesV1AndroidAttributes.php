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

namespace Google\Service\CloudIdentity;

class GoogleAppsCloudidentityDevicesV1AndroidAttributes extends \Google\Model
{
  /**
   * Ownership privilege is not set.
   */
  public const OWNERSHIP_PRIVILEGE_OWNERSHIP_PRIVILEGE_UNSPECIFIED = 'OWNERSHIP_PRIVILEGE_UNSPECIFIED';
  /**
   * Active device administrator privileges on the device.
   */
  public const OWNERSHIP_PRIVILEGE_DEVICE_ADMINISTRATOR = 'DEVICE_ADMINISTRATOR';
  /**
   * Profile Owner privileges. The account is in a managed corporate profile.
   */
  public const OWNERSHIP_PRIVILEGE_PROFILE_OWNER = 'PROFILE_OWNER';
  /**
   * Device Owner privileges on the device.
   */
  public const OWNERSHIP_PRIVILEGE_DEVICE_OWNER = 'DEVICE_OWNER';
  /**
   * Whether the device passes Android CTS compliance.
   *
   * @var bool
   */
  public $ctsProfileMatch;
  /**
   * Whether applications from unknown sources can be installed on device.
   *
   * @var bool
   */
  public $enabledUnknownSources;
  /**
   * Whether any potentially harmful apps were detected on the device.
   *
   * @var bool
   */
  public $hasPotentiallyHarmfulApps;
  /**
   * Whether this account is on an owner/primary profile. For phones, only true
   * for owner profiles. Android 4+ devices can have secondary or restricted
   * user profiles.
   *
   * @var bool
   */
  public $ownerProfileAccount;
  /**
   * Ownership privileges on device.
   *
   * @var string
   */
  public $ownershipPrivilege;
  /**
   * Whether device supports Android work profiles. If false, this service will
   * not block access to corp data even if an administrator turns on the
   * "Enforce Work Profile" policy.
   *
   * @var bool
   */
  public $supportsWorkProfile;
  /**
   * Whether Android verified boot status is GREEN.
   *
   * @var bool
   */
  public $verifiedBoot;
  /**
   * Whether Google Play Protect Verify Apps is enabled.
   *
   * @var bool
   */
  public $verifyAppsEnabled;

  /**
   * Whether the device passes Android CTS compliance.
   *
   * @param bool $ctsProfileMatch
   */
  public function setCtsProfileMatch($ctsProfileMatch)
  {
    $this->ctsProfileMatch = $ctsProfileMatch;
  }
  /**
   * @return bool
   */
  public function getCtsProfileMatch()
  {
    return $this->ctsProfileMatch;
  }
  /**
   * Whether applications from unknown sources can be installed on device.
   *
   * @param bool $enabledUnknownSources
   */
  public function setEnabledUnknownSources($enabledUnknownSources)
  {
    $this->enabledUnknownSources = $enabledUnknownSources;
  }
  /**
   * @return bool
   */
  public function getEnabledUnknownSources()
  {
    return $this->enabledUnknownSources;
  }
  /**
   * Whether any potentially harmful apps were detected on the device.
   *
   * @param bool $hasPotentiallyHarmfulApps
   */
  public function setHasPotentiallyHarmfulApps($hasPotentiallyHarmfulApps)
  {
    $this->hasPotentiallyHarmfulApps = $hasPotentiallyHarmfulApps;
  }
  /**
   * @return bool
   */
  public function getHasPotentiallyHarmfulApps()
  {
    return $this->hasPotentiallyHarmfulApps;
  }
  /**
   * Whether this account is on an owner/primary profile. For phones, only true
   * for owner profiles. Android 4+ devices can have secondary or restricted
   * user profiles.
   *
   * @param bool $ownerProfileAccount
   */
  public function setOwnerProfileAccount($ownerProfileAccount)
  {
    $this->ownerProfileAccount = $ownerProfileAccount;
  }
  /**
   * @return bool
   */
  public function getOwnerProfileAccount()
  {
    return $this->ownerProfileAccount;
  }
  /**
   * Ownership privileges on device.
   *
   * Accepted values: OWNERSHIP_PRIVILEGE_UNSPECIFIED, DEVICE_ADMINISTRATOR,
   * PROFILE_OWNER, DEVICE_OWNER
   *
   * @param self::OWNERSHIP_PRIVILEGE_* $ownershipPrivilege
   */
  public function setOwnershipPrivilege($ownershipPrivilege)
  {
    $this->ownershipPrivilege = $ownershipPrivilege;
  }
  /**
   * @return self::OWNERSHIP_PRIVILEGE_*
   */
  public function getOwnershipPrivilege()
  {
    return $this->ownershipPrivilege;
  }
  /**
   * Whether device supports Android work profiles. If false, this service will
   * not block access to corp data even if an administrator turns on the
   * "Enforce Work Profile" policy.
   *
   * @param bool $supportsWorkProfile
   */
  public function setSupportsWorkProfile($supportsWorkProfile)
  {
    $this->supportsWorkProfile = $supportsWorkProfile;
  }
  /**
   * @return bool
   */
  public function getSupportsWorkProfile()
  {
    return $this->supportsWorkProfile;
  }
  /**
   * Whether Android verified boot status is GREEN.
   *
   * @param bool $verifiedBoot
   */
  public function setVerifiedBoot($verifiedBoot)
  {
    $this->verifiedBoot = $verifiedBoot;
  }
  /**
   * @return bool
   */
  public function getVerifiedBoot()
  {
    return $this->verifiedBoot;
  }
  /**
   * Whether Google Play Protect Verify Apps is enabled.
   *
   * @param bool $verifyAppsEnabled
   */
  public function setVerifyAppsEnabled($verifyAppsEnabled)
  {
    $this->verifyAppsEnabled = $verifyAppsEnabled;
  }
  /**
   * @return bool
   */
  public function getVerifyAppsEnabled()
  {
    return $this->verifyAppsEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCloudidentityDevicesV1AndroidAttributes::class, 'Google_Service_CloudIdentity_GoogleAppsCloudidentityDevicesV1AndroidAttributes');
