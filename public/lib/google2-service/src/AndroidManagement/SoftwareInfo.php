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

class SoftwareInfo extends \Google\Model
{
  /**
   * Android build ID string meant for displaying to the user. For example,
   * shamu-userdebug 6.0.1 MOB30I 2756745 dev-keys.
   *
   * @var string
   */
  public $androidBuildNumber;
  /**
   * Build time.
   *
   * @var string
   */
  public $androidBuildTime;
  /**
   * The Android Device Policy app version code.
   *
   * @var int
   */
  public $androidDevicePolicyVersionCode;
  /**
   * The Android Device Policy app version as displayed to the user.
   *
   * @var string
   */
  public $androidDevicePolicyVersionName;
  /**
   * The user-visible Android version string. For example, 6.0.1.
   *
   * @var string
   */
  public $androidVersion;
  /**
   * The system bootloader version number, e.g. 0.6.7.
   *
   * @var string
   */
  public $bootloaderVersion;
  /**
   * SHA-256 hash of android.content.pm.Signature
   * (https://developer.android.com/reference/android/content/pm/Signature.html)
   * associated with the system package, which can be used to verify that the
   * system build hasn't been modified.
   *
   * @var string
   */
  public $deviceBuildSignature;
  /**
   * Kernel version, for example, 2.6.32.9-g103d848.
   *
   * @var string
   */
  public $deviceKernelVersion;
  /**
   * An IETF BCP 47 language code for the primary locale on the device.
   *
   * @var string
   */
  public $primaryLanguageCode;
  /**
   * Security patch level, e.g. 2016-05-01.
   *
   * @var string
   */
  public $securityPatchLevel;
  protected $systemUpdateInfoType = SystemUpdateInfo::class;
  protected $systemUpdateInfoDataType = '';

  /**
   * Android build ID string meant for displaying to the user. For example,
   * shamu-userdebug 6.0.1 MOB30I 2756745 dev-keys.
   *
   * @param string $androidBuildNumber
   */
  public function setAndroidBuildNumber($androidBuildNumber)
  {
    $this->androidBuildNumber = $androidBuildNumber;
  }
  /**
   * @return string
   */
  public function getAndroidBuildNumber()
  {
    return $this->androidBuildNumber;
  }
  /**
   * Build time.
   *
   * @param string $androidBuildTime
   */
  public function setAndroidBuildTime($androidBuildTime)
  {
    $this->androidBuildTime = $androidBuildTime;
  }
  /**
   * @return string
   */
  public function getAndroidBuildTime()
  {
    return $this->androidBuildTime;
  }
  /**
   * The Android Device Policy app version code.
   *
   * @param int $androidDevicePolicyVersionCode
   */
  public function setAndroidDevicePolicyVersionCode($androidDevicePolicyVersionCode)
  {
    $this->androidDevicePolicyVersionCode = $androidDevicePolicyVersionCode;
  }
  /**
   * @return int
   */
  public function getAndroidDevicePolicyVersionCode()
  {
    return $this->androidDevicePolicyVersionCode;
  }
  /**
   * The Android Device Policy app version as displayed to the user.
   *
   * @param string $androidDevicePolicyVersionName
   */
  public function setAndroidDevicePolicyVersionName($androidDevicePolicyVersionName)
  {
    $this->androidDevicePolicyVersionName = $androidDevicePolicyVersionName;
  }
  /**
   * @return string
   */
  public function getAndroidDevicePolicyVersionName()
  {
    return $this->androidDevicePolicyVersionName;
  }
  /**
   * The user-visible Android version string. For example, 6.0.1.
   *
   * @param string $androidVersion
   */
  public function setAndroidVersion($androidVersion)
  {
    $this->androidVersion = $androidVersion;
  }
  /**
   * @return string
   */
  public function getAndroidVersion()
  {
    return $this->androidVersion;
  }
  /**
   * The system bootloader version number, e.g. 0.6.7.
   *
   * @param string $bootloaderVersion
   */
  public function setBootloaderVersion($bootloaderVersion)
  {
    $this->bootloaderVersion = $bootloaderVersion;
  }
  /**
   * @return string
   */
  public function getBootloaderVersion()
  {
    return $this->bootloaderVersion;
  }
  /**
   * SHA-256 hash of android.content.pm.Signature
   * (https://developer.android.com/reference/android/content/pm/Signature.html)
   * associated with the system package, which can be used to verify that the
   * system build hasn't been modified.
   *
   * @param string $deviceBuildSignature
   */
  public function setDeviceBuildSignature($deviceBuildSignature)
  {
    $this->deviceBuildSignature = $deviceBuildSignature;
  }
  /**
   * @return string
   */
  public function getDeviceBuildSignature()
  {
    return $this->deviceBuildSignature;
  }
  /**
   * Kernel version, for example, 2.6.32.9-g103d848.
   *
   * @param string $deviceKernelVersion
   */
  public function setDeviceKernelVersion($deviceKernelVersion)
  {
    $this->deviceKernelVersion = $deviceKernelVersion;
  }
  /**
   * @return string
   */
  public function getDeviceKernelVersion()
  {
    return $this->deviceKernelVersion;
  }
  /**
   * An IETF BCP 47 language code for the primary locale on the device.
   *
   * @param string $primaryLanguageCode
   */
  public function setPrimaryLanguageCode($primaryLanguageCode)
  {
    $this->primaryLanguageCode = $primaryLanguageCode;
  }
  /**
   * @return string
   */
  public function getPrimaryLanguageCode()
  {
    return $this->primaryLanguageCode;
  }
  /**
   * Security patch level, e.g. 2016-05-01.
   *
   * @param string $securityPatchLevel
   */
  public function setSecurityPatchLevel($securityPatchLevel)
  {
    $this->securityPatchLevel = $securityPatchLevel;
  }
  /**
   * @return string
   */
  public function getSecurityPatchLevel()
  {
    return $this->securityPatchLevel;
  }
  /**
   * Information about a potential pending system update.
   *
   * @param SystemUpdateInfo $systemUpdateInfo
   */
  public function setSystemUpdateInfo(SystemUpdateInfo $systemUpdateInfo)
  {
    $this->systemUpdateInfo = $systemUpdateInfo;
  }
  /**
   * @return SystemUpdateInfo
   */
  public function getSystemUpdateInfo()
  {
    return $this->systemUpdateInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SoftwareInfo::class, 'Google_Service_AndroidManagement_SoftwareInfo');
