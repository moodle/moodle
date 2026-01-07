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

class DeviceSettings extends \Google\Model
{
  /**
   * Unspecified. No device should have this type.
   */
  public const ENCRYPTION_STATUS_ENCRYPTION_STATUS_UNSPECIFIED = 'ENCRYPTION_STATUS_UNSPECIFIED';
  /**
   * Encryption is not supported by the device.
   */
  public const ENCRYPTION_STATUS_UNSUPPORTED = 'UNSUPPORTED';
  /**
   * Encryption is supported by the device, but is not currently active.
   */
  public const ENCRYPTION_STATUS_INACTIVE = 'INACTIVE';
  /**
   * Encryption is not currently active, but is currently being activated.
   */
  public const ENCRYPTION_STATUS_ACTIVATING = 'ACTIVATING';
  /**
   * Encryption is active.
   */
  public const ENCRYPTION_STATUS_ACTIVE = 'ACTIVE';
  /**
   * Encryption is active, but an encryption key is not set by the user.
   */
  public const ENCRYPTION_STATUS_ACTIVE_DEFAULT_KEY = 'ACTIVE_DEFAULT_KEY';
  /**
   * Encryption is active, and the encryption key is tied to the user profile.
   */
  public const ENCRYPTION_STATUS_ACTIVE_PER_USER = 'ACTIVE_PER_USER';
  /**
   * Whether ADB (https://developer.android.com/studio/command-line/adb.html) is
   * enabled on the device.
   *
   * @var bool
   */
  public $adbEnabled;
  /**
   * Whether developer mode is enabled on the device.
   *
   * @var bool
   */
  public $developmentSettingsEnabled;
  /**
   * Encryption status from DevicePolicyManager.
   *
   * @var string
   */
  public $encryptionStatus;
  /**
   * Whether the device is secured with PIN/password.
   *
   * @var bool
   */
  public $isDeviceSecure;
  /**
   * Whether the storage encryption is enabled.
   *
   * @var bool
   */
  public $isEncrypted;
  /**
   * Whether installing apps from unknown sources is enabled.
   *
   * @var bool
   */
  public $unknownSourcesEnabled;
  /**
   * Whether Google Play Protect verification
   * (https://support.google.com/accounts/answer/2812853) is enforced on the
   * device.
   *
   * @var bool
   */
  public $verifyAppsEnabled;

  /**
   * Whether ADB (https://developer.android.com/studio/command-line/adb.html) is
   * enabled on the device.
   *
   * @param bool $adbEnabled
   */
  public function setAdbEnabled($adbEnabled)
  {
    $this->adbEnabled = $adbEnabled;
  }
  /**
   * @return bool
   */
  public function getAdbEnabled()
  {
    return $this->adbEnabled;
  }
  /**
   * Whether developer mode is enabled on the device.
   *
   * @param bool $developmentSettingsEnabled
   */
  public function setDevelopmentSettingsEnabled($developmentSettingsEnabled)
  {
    $this->developmentSettingsEnabled = $developmentSettingsEnabled;
  }
  /**
   * @return bool
   */
  public function getDevelopmentSettingsEnabled()
  {
    return $this->developmentSettingsEnabled;
  }
  /**
   * Encryption status from DevicePolicyManager.
   *
   * Accepted values: ENCRYPTION_STATUS_UNSPECIFIED, UNSUPPORTED, INACTIVE,
   * ACTIVATING, ACTIVE, ACTIVE_DEFAULT_KEY, ACTIVE_PER_USER
   *
   * @param self::ENCRYPTION_STATUS_* $encryptionStatus
   */
  public function setEncryptionStatus($encryptionStatus)
  {
    $this->encryptionStatus = $encryptionStatus;
  }
  /**
   * @return self::ENCRYPTION_STATUS_*
   */
  public function getEncryptionStatus()
  {
    return $this->encryptionStatus;
  }
  /**
   * Whether the device is secured with PIN/password.
   *
   * @param bool $isDeviceSecure
   */
  public function setIsDeviceSecure($isDeviceSecure)
  {
    $this->isDeviceSecure = $isDeviceSecure;
  }
  /**
   * @return bool
   */
  public function getIsDeviceSecure()
  {
    return $this->isDeviceSecure;
  }
  /**
   * Whether the storage encryption is enabled.
   *
   * @param bool $isEncrypted
   */
  public function setIsEncrypted($isEncrypted)
  {
    $this->isEncrypted = $isEncrypted;
  }
  /**
   * @return bool
   */
  public function getIsEncrypted()
  {
    return $this->isEncrypted;
  }
  /**
   * Whether installing apps from unknown sources is enabled.
   *
   * @param bool $unknownSourcesEnabled
   */
  public function setUnknownSourcesEnabled($unknownSourcesEnabled)
  {
    $this->unknownSourcesEnabled = $unknownSourcesEnabled;
  }
  /**
   * @return bool
   */
  public function getUnknownSourcesEnabled()
  {
    return $this->unknownSourcesEnabled;
  }
  /**
   * Whether Google Play Protect verification
   * (https://support.google.com/accounts/answer/2812853) is enforced on the
   * device.
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
class_alias(DeviceSettings::class, 'Google_Service_AndroidManagement_DeviceSettings');
