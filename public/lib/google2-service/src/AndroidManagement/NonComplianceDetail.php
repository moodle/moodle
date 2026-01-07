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

class NonComplianceDetail extends \Google\Model
{
  /**
   * This value is disallowed.
   */
  public const INSTALLATION_FAILURE_REASON_INSTALLATION_FAILURE_REASON_UNSPECIFIED = 'INSTALLATION_FAILURE_REASON_UNSPECIFIED';
  /**
   * An unknown condition is preventing the app from being installed. Some
   * potential reasons are that the device doesn't have enough storage, the
   * device network connection is unreliable, or the installation is taking
   * longer than expected. The installation will be retried automatically.
   */
  public const INSTALLATION_FAILURE_REASON_INSTALLATION_FAILURE_REASON_UNKNOWN = 'INSTALLATION_FAILURE_REASON_UNKNOWN';
  /**
   * The installation is still in progress.
   */
  public const INSTALLATION_FAILURE_REASON_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The app was not found in Play.
   */
  public const INSTALLATION_FAILURE_REASON_NOT_FOUND = 'NOT_FOUND';
  /**
   * The app is incompatible with the device.
   */
  public const INSTALLATION_FAILURE_REASON_NOT_COMPATIBLE_WITH_DEVICE = 'NOT_COMPATIBLE_WITH_DEVICE';
  /**
   * The app has not been approved by the admin.
   */
  public const INSTALLATION_FAILURE_REASON_NOT_APPROVED = 'NOT_APPROVED';
  /**
   * The app has new permissions that have not been accepted by the admin.
   */
  public const INSTALLATION_FAILURE_REASON_PERMISSIONS_NOT_ACCEPTED = 'PERMISSIONS_NOT_ACCEPTED';
  /**
   * The app is not available in the user's country.
   */
  public const INSTALLATION_FAILURE_REASON_NOT_AVAILABLE_IN_COUNTRY = 'NOT_AVAILABLE_IN_COUNTRY';
  /**
   * There are no licenses available to assign to the user.
   */
  public const INSTALLATION_FAILURE_REASON_NO_LICENSES_REMAINING = 'NO_LICENSES_REMAINING';
  /**
   * The enterprise is no longer enrolled with Managed Google Play or the admin
   * has not accepted the latest Managed Google Play Terms of Service.
   */
  public const INSTALLATION_FAILURE_REASON_NOT_ENROLLED = 'NOT_ENROLLED';
  /**
   * The user is no longer valid. The user may have been deleted or disabled.
   */
  public const INSTALLATION_FAILURE_REASON_USER_INVALID = 'USER_INVALID';
  /**
   * A network error on the user's device has prevented the install from
   * succeeding. This usually happens when the device's internet connectivity is
   * degraded, unavailable or there's a network configuration issue. Please
   * ensure the device has access to full internet connectivity on a network
   * that meets Android Enterprise Network Requirements
   * (https://support.google.com/work/android/answer/10513641). App install or
   * update will automatically resume once this is the case.
   */
  public const INSTALLATION_FAILURE_REASON_NETWORK_ERROR_UNRELIABLE_CONNECTION = 'NETWORK_ERROR_UNRELIABLE_CONNECTION';
  /**
   * The user's device does not have sufficient storage space to install the
   * app. This can be resolved by clearing up storage space on the device. App
   * install or update will automatically resume once the device has sufficient
   * storage.
   */
  public const INSTALLATION_FAILURE_REASON_INSUFFICIENT_STORAGE = 'INSUFFICIENT_STORAGE';
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
   * Specific non-compliance reason is not specified. Fields in
   * specific_non_compliance_context are not set.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_SPECIFIC_NON_COMPLIANCE_REASON_UNSPECIFIED = 'SPECIFIC_NON_COMPLIANCE_REASON_UNSPECIFIED';
  /**
   * User needs to confirm credentials by entering the screen lock. Fields in
   * specific_non_compliance_context are not set. nonComplianceReason is set to
   * USER_ACTION.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_PASSWORD_POLICIES_USER_CREDENTIALS_CONFIRMATION_REQUIRED = 'PASSWORD_POLICIES_USER_CREDENTIALS_CONFIRMATION_REQUIRED';
  /**
   * The device or profile password has expired. passwordPoliciesContext is set.
   * nonComplianceReason is set to USER_ACTION.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_PASSWORD_POLICIES_PASSWORD_EXPIRED = 'PASSWORD_POLICIES_PASSWORD_EXPIRED';
  /**
   * The device password does not satisfy password requirements.
   * passwordPoliciesContext is set. nonComplianceReason is set to USER_ACTION.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_PASSWORD_POLICIES_PASSWORD_NOT_SUFFICIENT = 'PASSWORD_POLICIES_PASSWORD_NOT_SUFFICIENT';
  /**
   * There is an incorrect value in ONC Wi-Fi configuration. fieldPath specifies
   * which field value is incorrect. oncWifiContext is set. nonComplianceReason
   * is set to INVALID_VALUE.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_ONC_WIFI_INVALID_VALUE = 'ONC_WIFI_INVALID_VALUE';
  /**
   * The ONC Wi-Fi setting is not supported in the API level of the Android
   * version running on the device. fieldPath specifies which field value is not
   * supported. oncWifiContext is set. nonComplianceReason is set to API_LEVEL.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_ONC_WIFI_API_LEVEL = 'ONC_WIFI_API_LEVEL';
  /**
   * The enterprise Wi-Fi network is missing either the root CA or domain name.
   * nonComplianceReason is set to INVALID_VALUE.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_ONC_WIFI_INVALID_ENTERPRISE_CONFIG = 'ONC_WIFI_INVALID_ENTERPRISE_CONFIG';
  /**
   * User needs to remove the configured Wi-Fi network manually. This is
   * applicable only on work profiles on personally-owned devices.
   * nonComplianceReason is set to USER_ACTION.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_ONC_WIFI_USER_SHOULD_REMOVE_NETWORK = 'ONC_WIFI_USER_SHOULD_REMOVE_NETWORK';
  /**
   * Key pair alias specified via ClientCertKeyPairAlias (https://chromium.googl
   * esource.com/chromium/src/+/main/components/onc/docs/onc_spec.md#eap-type)
   * field in openNetworkConfiguration does not correspond to an existing key
   * installed on the device. nonComplianceReason is set to INVALID_VALUE.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_ONC_WIFI_KEY_PAIR_ALIAS_NOT_CORRESPONDING_TO_EXISTING_KEY = 'ONC_WIFI_KEY_PAIR_ALIAS_NOT_CORRESPONDING_TO_EXISTING_KEY';
  /**
   * This policy setting is restricted and cannot be set for this Google Cloud
   * Platform project. More details (including how to enable usage of this
   * policy setting) are available in the Permissible Usage policy
   * (https://developers.google.com/android/management/permissible-usage).
   * nonComplianceReason is set to PROJECT_NOT_PERMITTED.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_PERMISSIBLE_USAGE_RESTRICTION = 'PERMISSIBLE_USAGE_RESTRICTION';
  /**
   * Work account required by the workAccountSetupConfig policy setting is not
   * part of the enterprise anymore. nonComplianceReason is set to USER_ACTION.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_REQUIRED_ACCOUNT_NOT_IN_ENTERPRISE = 'REQUIRED_ACCOUNT_NOT_IN_ENTERPRISE';
  /**
   * Work account added by the user is not part of the enterprise.
   * nonComplianceReason is set to USER_ACTION.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_NEW_ACCOUNT_NOT_IN_ENTERPRISE = 'NEW_ACCOUNT_NOT_IN_ENTERPRISE';
  /**
   * The default application setting is applied to the scopes that are not
   * supported by the management mode, even if the management mode itself is
   * supported for the app type (e.g., a policy with DEFAULT_BROWSER app type
   * and SCOPE_PERSONAL_PROFILE list sent to a fully managed device results in
   * the scopes being inapplicable for the management mode). If the management
   * mode is not supported for the app type, a NonComplianceDetail with
   * MANAGEMENT_MODE is reported, without a
   * specificNonComplianceReason.nonComplianceReason is set to MANAGEMENT_MODE.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_DEFAULT_APPLICATION_SETTING_UNSUPPORTED_SCOPES = 'DEFAULT_APPLICATION_SETTING_UNSUPPORTED_SCOPES';
  /**
   * The default application setting failed to apply for a specific scope.
   * defaultApplicationContext is set. nonComplianceReason is set to
   * INVALID_VALUE or APP_NOT_INSTALLED.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_DEFAULT_APPLICATION_SETTING_FAILED_FOR_SCOPE = 'DEFAULT_APPLICATION_SETTING_FAILED_FOR_SCOPE';
  /**
   * The specified host for private DNS is a valid hostname but was found to not
   * be a private DNS server. nonComplianceReason is set to INVALID_VALUE.
   */
  public const SPECIFIC_NON_COMPLIANCE_REASON_PRIVATE_DNS_HOST_NOT_SERVING = 'PRIVATE_DNS_HOST_NOT_SERVING';
  /**
   * If the policy setting could not be applied, the current value of the
   * setting on the device.
   *
   * @var array
   */
  public $currentValue;
  /**
   * For settings with nested fields, if a particular nested field is out of
   * compliance, this specifies the full path to the offending field. The path
   * is formatted in the same way the policy JSON field would be referenced in
   * JavaScript, that is: 1) For object-typed fields, the field name is followed
   * by a dot then by a subfield name. 2) For array-typed fields, the field name
   * is followed by the array index enclosed in brackets. For example, to
   * indicate a problem with the url field in the externalData field in the 3rd
   * application, the path would be applications[2].externalData.url
   *
   * @var string
   */
  public $fieldPath;
  /**
   * If package_name is set and the non-compliance reason is APP_NOT_INSTALLED
   * or APP_NOT_UPDATED, the detailed reason the app can't be installed or
   * updated.
   *
   * @var string
   */
  public $installationFailureReason;
  /**
   * The reason the device is not in compliance with the setting.
   *
   * @var string
   */
  public $nonComplianceReason;
  /**
   * The package name indicating which app is out of compliance, if applicable.
   *
   * @var string
   */
  public $packageName;
  /**
   * The name of the policy setting. This is the JSON field name of a top-level
   * Policy field.
   *
   * @var string
   */
  public $settingName;
  protected $specificNonComplianceContextType = SpecificNonComplianceContext::class;
  protected $specificNonComplianceContextDataType = '';
  /**
   * The policy-specific reason the device is not in compliance with the
   * setting.
   *
   * @var string
   */
  public $specificNonComplianceReason;

  /**
   * If the policy setting could not be applied, the current value of the
   * setting on the device.
   *
   * @param array $currentValue
   */
  public function setCurrentValue($currentValue)
  {
    $this->currentValue = $currentValue;
  }
  /**
   * @return array
   */
  public function getCurrentValue()
  {
    return $this->currentValue;
  }
  /**
   * For settings with nested fields, if a particular nested field is out of
   * compliance, this specifies the full path to the offending field. The path
   * is formatted in the same way the policy JSON field would be referenced in
   * JavaScript, that is: 1) For object-typed fields, the field name is followed
   * by a dot then by a subfield name. 2) For array-typed fields, the field name
   * is followed by the array index enclosed in brackets. For example, to
   * indicate a problem with the url field in the externalData field in the 3rd
   * application, the path would be applications[2].externalData.url
   *
   * @param string $fieldPath
   */
  public function setFieldPath($fieldPath)
  {
    $this->fieldPath = $fieldPath;
  }
  /**
   * @return string
   */
  public function getFieldPath()
  {
    return $this->fieldPath;
  }
  /**
   * If package_name is set and the non-compliance reason is APP_NOT_INSTALLED
   * or APP_NOT_UPDATED, the detailed reason the app can't be installed or
   * updated.
   *
   * Accepted values: INSTALLATION_FAILURE_REASON_UNSPECIFIED,
   * INSTALLATION_FAILURE_REASON_UNKNOWN, IN_PROGRESS, NOT_FOUND,
   * NOT_COMPATIBLE_WITH_DEVICE, NOT_APPROVED, PERMISSIONS_NOT_ACCEPTED,
   * NOT_AVAILABLE_IN_COUNTRY, NO_LICENSES_REMAINING, NOT_ENROLLED,
   * USER_INVALID, NETWORK_ERROR_UNRELIABLE_CONNECTION, INSUFFICIENT_STORAGE
   *
   * @param self::INSTALLATION_FAILURE_REASON_* $installationFailureReason
   */
  public function setInstallationFailureReason($installationFailureReason)
  {
    $this->installationFailureReason = $installationFailureReason;
  }
  /**
   * @return self::INSTALLATION_FAILURE_REASON_*
   */
  public function getInstallationFailureReason()
  {
    return $this->installationFailureReason;
  }
  /**
   * The reason the device is not in compliance with the setting.
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
   * The package name indicating which app is out of compliance, if applicable.
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
   * Policy field.
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
  /**
   * Additional context for specific_non_compliance_reason.
   *
   * @param SpecificNonComplianceContext $specificNonComplianceContext
   */
  public function setSpecificNonComplianceContext(SpecificNonComplianceContext $specificNonComplianceContext)
  {
    $this->specificNonComplianceContext = $specificNonComplianceContext;
  }
  /**
   * @return SpecificNonComplianceContext
   */
  public function getSpecificNonComplianceContext()
  {
    return $this->specificNonComplianceContext;
  }
  /**
   * The policy-specific reason the device is not in compliance with the
   * setting.
   *
   * Accepted values: SPECIFIC_NON_COMPLIANCE_REASON_UNSPECIFIED,
   * PASSWORD_POLICIES_USER_CREDENTIALS_CONFIRMATION_REQUIRED,
   * PASSWORD_POLICIES_PASSWORD_EXPIRED,
   * PASSWORD_POLICIES_PASSWORD_NOT_SUFFICIENT, ONC_WIFI_INVALID_VALUE,
   * ONC_WIFI_API_LEVEL, ONC_WIFI_INVALID_ENTERPRISE_CONFIG,
   * ONC_WIFI_USER_SHOULD_REMOVE_NETWORK,
   * ONC_WIFI_KEY_PAIR_ALIAS_NOT_CORRESPONDING_TO_EXISTING_KEY,
   * PERMISSIBLE_USAGE_RESTRICTION, REQUIRED_ACCOUNT_NOT_IN_ENTERPRISE,
   * NEW_ACCOUNT_NOT_IN_ENTERPRISE,
   * DEFAULT_APPLICATION_SETTING_UNSUPPORTED_SCOPES,
   * DEFAULT_APPLICATION_SETTING_FAILED_FOR_SCOPE, PRIVATE_DNS_HOST_NOT_SERVING
   *
   * @param self::SPECIFIC_NON_COMPLIANCE_REASON_* $specificNonComplianceReason
   */
  public function setSpecificNonComplianceReason($specificNonComplianceReason)
  {
    $this->specificNonComplianceReason = $specificNonComplianceReason;
  }
  /**
   * @return self::SPECIFIC_NON_COMPLIANCE_REASON_*
   */
  public function getSpecificNonComplianceReason()
  {
    return $this->specificNonComplianceReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NonComplianceDetail::class, 'Google_Service_AndroidManagement_NonComplianceDetail');
