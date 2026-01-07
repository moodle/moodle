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

class ApnPolicy extends \Google\Collection
{
  /**
   * Unspecified. Defaults to OVERRIDE_APNS_DISABLED.
   */
  public const OVERRIDE_APNS_OVERRIDE_APNS_UNSPECIFIED = 'OVERRIDE_APNS_UNSPECIFIED';
  /**
   * Override APNs disabled. Any configured apnSettings are saved on the device,
   * but are disabled and have no effect. Any other APNs on the device remain in
   * use.
   */
  public const OVERRIDE_APNS_OVERRIDE_APNS_DISABLED = 'OVERRIDE_APNS_DISABLED';
  /**
   * Override APNs enabled. Only override APNs are in use, any other APNs are
   * ignored. This can only be set on fully managed devices on Android 10 and
   * above. For work profiles override APNs are enabled via
   * preferentialNetworkServiceSettings and this value cannot be set. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 10. A NonComplianceDetail with MANAGEMENT_MODE is reported for
   * work profiles.
   */
  public const OVERRIDE_APNS_OVERRIDE_APNS_ENABLED = 'OVERRIDE_APNS_ENABLED';
  protected $collection_key = 'apnSettings';
  protected $apnSettingsType = ApnSetting::class;
  protected $apnSettingsDataType = 'array';
  /**
   * Optional. Whether override APNs are disabled or enabled. See
   * DevicePolicyManager.setOverrideApnsEnabled (https://developer.android.com/r
   * eference/android/app/admin/DevicePolicyManager#setOverrideApnsEnabled) for
   * more details.
   *
   * @var string
   */
  public $overrideApns;

  /**
   * Optional. APN settings for override APNs. There must not be any conflict
   * between any of APN settings provided, otherwise the policy will be
   * rejected. Two ApnSettings are considered to conflict when all of the
   * following fields match on both: numericOperatorId, apn, proxyAddress,
   * proxyPort, mmsProxyAddress, mmsProxyPort, mmsc, mvnoType, protocol,
   * roamingProtocol. If some of the APN settings result in non-compliance of
   * INVALID_VALUE , they will be ignored. This can be set on fully managed
   * devices on Android 10 and above. This can also be set on work profiles on
   * Android 13 and above and only with ApnSetting's with ENTERPRISE APN type. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 10. A NonComplianceDetail with MANAGEMENT_MODE is reported for
   * work profiles on Android versions less than 13.
   *
   * @param ApnSetting[] $apnSettings
   */
  public function setApnSettings($apnSettings)
  {
    $this->apnSettings = $apnSettings;
  }
  /**
   * @return ApnSetting[]
   */
  public function getApnSettings()
  {
    return $this->apnSettings;
  }
  /**
   * Optional. Whether override APNs are disabled or enabled. See
   * DevicePolicyManager.setOverrideApnsEnabled (https://developer.android.com/r
   * eference/android/app/admin/DevicePolicyManager#setOverrideApnsEnabled) for
   * more details.
   *
   * Accepted values: OVERRIDE_APNS_UNSPECIFIED, OVERRIDE_APNS_DISABLED,
   * OVERRIDE_APNS_ENABLED
   *
   * @param self::OVERRIDE_APNS_* $overrideApns
   */
  public function setOverrideApns($overrideApns)
  {
    $this->overrideApns = $overrideApns;
  }
  /**
   * @return self::OVERRIDE_APNS_*
   */
  public function getOverrideApns()
  {
    return $this->overrideApns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApnPolicy::class, 'Google_Service_AndroidManagement_ApnPolicy');
