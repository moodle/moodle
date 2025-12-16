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

class NonComplianceDetailCondition extends \Google\Model
{
  /**
   * This value is not used.
   */
  public const NON_COMPLIANCE_REASON_NON_COMPLIANCE_REASON_UNSPECIFIED = 'NON_COMPLIANCE_REASON_UNSPECIFIED';
  /**
   * The setting is not supported in the API level of the Android version
   * running on the device.
   */
  public const NON_COMPLIANCE_REASON_API_LEVEL = 'API_LEVEL';
  /**
   * The management mode (such as fully managed or work profile) doesn't support
   * the setting.
   */
  public const NON_COMPLIANCE_REASON_MANAGEMENT_MODE = 'MANAGEMENT_MODE';
  /**
   * The user has not taken required action to comply with the setting.
   */
  public const NON_COMPLIANCE_REASON_USER_ACTION = 'USER_ACTION';
  /**
   * The setting has an invalid value.
   */
  public const NON_COMPLIANCE_REASON_INVALID_VALUE = 'INVALID_VALUE';
  /**
   * The app required to implement the policy is not installed.
   */
  public const NON_COMPLIANCE_REASON_APP_NOT_INSTALLED = 'APP_NOT_INSTALLED';
  /**
   * The policy is not supported by the version of Android Device Policy on the
   * device.
   */
  public const NON_COMPLIANCE_REASON_UNSUPPORTED = 'UNSUPPORTED';
  /**
   * A blocked app is installed.
   */
  public const NON_COMPLIANCE_REASON_APP_INSTALLED = 'APP_INSTALLED';
  /**
   * The setting hasn't been applied at the time of the report, but is expected
   * to be applied shortly.
   */
  public const NON_COMPLIANCE_REASON_PENDING = 'PENDING';
  /**
   * The setting can't be applied to the app because the app doesn't support it,
   * for example because its target SDK version is not high enough.
   */
  public const NON_COMPLIANCE_REASON_APP_INCOMPATIBLE = 'APP_INCOMPATIBLE';
  /**
   * The app is installed, but it hasn't been updated to the minimum version
   * code specified by policy.
   */
  public const NON_COMPLIANCE_REASON_APP_NOT_UPDATED = 'APP_NOT_UPDATED';
  /**
   * The device is incompatible with the policy requirements.
   */
  public const NON_COMPLIANCE_REASON_DEVICE_INCOMPATIBLE = 'DEVICE_INCOMPATIBLE';
  /**
   * The app's signing certificate does not match the setting value.
   */
  public const NON_COMPLIANCE_REASON_APP_SIGNING_CERT_MISMATCH = 'APP_SIGNING_CERT_MISMATCH';
  /**
   * The Google Cloud Platform project used to manage the device is not
   * permitted to use this policy.
   */
  public const NON_COMPLIANCE_REASON_PROJECT_NOT_PERMITTED = 'PROJECT_NOT_PERMITTED';
  /**
   * The reason the device is not in compliance with the setting. If not set,
   * then this condition matches any reason.
   *
   * @var string
   */
  public $nonComplianceReason;
  /**
   * The package name of the app that's out of compliance. If not set, then this
   * condition matches any package name.
   *
   * @var string
   */
  public $packageName;
  /**
   * The name of the policy setting. This is the JSON field name of a top-level
   * Policy field. If not set, then this condition matches any setting name.
   *
   * @var string
   */
  public $settingName;

  /**
   * The reason the device is not in compliance with the setting. If not set,
   * then this condition matches any reason.
   *
   * Accepted values: NON_COMPLIANCE_REASON_UNSPECIFIED, API_LEVEL,
   * MANAGEMENT_MODE, USER_ACTION, INVALID_VALUE, APP_NOT_INSTALLED,
   * UNSUPPORTED, APP_INSTALLED, PENDING, APP_INCOMPATIBLE, APP_NOT_UPDATED,
   * DEVICE_INCOMPATIBLE, APP_SIGNING_CERT_MISMATCH, PROJECT_NOT_PERMITTED
   *
   * @param self::NON_COMPLIANCE_REASON_* $nonComplianceReason
   */
  public function setNonComplianceReason($nonComplianceReason)
  {
    $this->nonComplianceReason = $nonComplianceReason;
  }
  /**
   * @return self::NON_COMPLIANCE_REASON_*
   */
  public function getNonComplianceReason()
  {
    return $this->nonComplianceReason;
  }
  /**
   * The package name of the app that's out of compliance. If not set, then this
   * condition matches any package name.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
  /**
   * The name of the policy setting. This is the JSON field name of a top-level
   * Policy field. If not set, then this condition matches any setting name.
   *
   * @param string $settingName
   */
  public function setSettingName($settingName)
  {
    $this->settingName = $settingName;
  }
  /**
   * @return string
   */
  public function getSettingName()
  {
    return $this->settingName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NonComplianceDetailCondition::class, 'Google_Service_AndroidManagement_NonComplianceDetailCondition');
