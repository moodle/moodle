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

class DefaultApplicationSetting extends \Google\Collection
{
  /**
   * Unspecified. This value must not be used.
   */
  public const DEFAULT_APPLICATION_TYPE_DEFAULT_APPLICATION_TYPE_UNSPECIFIED = 'DEFAULT_APPLICATION_TYPE_UNSPECIFIED';
  /**
   * The assistant app type. This app type is only allowed to be set for
   * SCOPE_FULLY_MANAGED.Supported on fully managed devices on Android 16 and
   * above. A NonComplianceDetail with MANAGEMENT_MODE is reported for other
   * management modes. A NonComplianceDetail with API_LEVEL is reported if the
   * Android version is less than 16.
   */
  public const DEFAULT_APPLICATION_TYPE_DEFAULT_ASSISTANT = 'DEFAULT_ASSISTANT';
  /**
   * The browser app type.Supported on Android 16 and above. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 16.
   */
  public const DEFAULT_APPLICATION_TYPE_DEFAULT_BROWSER = 'DEFAULT_BROWSER';
  /**
   * The call redirection app type. This app type cannot be set for
   * SCOPE_PERSONAL_PROFILE.Supported on Android 16 and above. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 16.
   */
  public const DEFAULT_APPLICATION_TYPE_DEFAULT_CALL_REDIRECTION = 'DEFAULT_CALL_REDIRECTION';
  /**
   * The call screening app type. This app type cannot be set for
   * SCOPE_PERSONAL_PROFILE.Supported on Android 16 and above. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 16.
   */
  public const DEFAULT_APPLICATION_TYPE_DEFAULT_CALL_SCREENING = 'DEFAULT_CALL_SCREENING';
  /**
   * The dialer app type.Supported on fully managed devices on Android 14 and
   * 15. A NonComplianceDetail with MANAGEMENT_MODE is reported for other
   * management modes. A NonComplianceDetail with API_LEVEL is reported if the
   * Android version is less than 14.Supported on all management modes on
   * Android 16 and above.
   */
  public const DEFAULT_APPLICATION_TYPE_DEFAULT_DIALER = 'DEFAULT_DIALER';
  /**
   * The home app type. This app type is only allowed to be set for
   * SCOPE_FULLY_MANAGED.Supported on fully managed devices on Android 16 and
   * above. A NonComplianceDetail with MANAGEMENT_MODE is reported for other
   * management modes. A NonComplianceDetail with API_LEVEL is reported if the
   * Android version is less than 16.
   */
  public const DEFAULT_APPLICATION_TYPE_DEFAULT_HOME = 'DEFAULT_HOME';
  /**
   * The SMS app type. This app type cannot be set for
   * SCOPE_WORK_PROFILE.Supported on company-owned devices on Android 16 and
   * above. A NonComplianceDetail with MANAGEMENT_MODE is reported for
   * personally-owned devices. A NonComplianceDetail with API_LEVEL is reported
   * if the Android version is less than 16.
   */
  public const DEFAULT_APPLICATION_TYPE_DEFAULT_SMS = 'DEFAULT_SMS';
  /**
   * The wallet app type. The default application of this type applies across
   * profiles.On a company-owned device with a work profile, admins can set the
   * scope to SCOPE_PERSONAL_PROFILE to set a personal profile pre-installed
   * system app as the default, or to SCOPE_WORK_PROFILE to set a work profile
   * app as the default. It is not allowed to specify both scopes at the same
   * time.Due to a known issue, the user may be able to change the default
   * wallet even when this is set on a fully managed device.Supported on
   * company-owned devices on Android 16 and above. A NonComplianceDetail with
   * MANAGEMENT_MODE is reported for personally-owned devices. A
   * NonComplianceDetail with API_LEVEL is reported if the Android version is
   * less than 16.
   */
  public const DEFAULT_APPLICATION_TYPE_DEFAULT_WALLET = 'DEFAULT_WALLET';
  protected $collection_key = 'defaultApplications';
  /**
   * Required. The scopes to which the policy should be applied. This list must
   * not be empty or contain duplicates.A NonComplianceDetail with
   * MANAGEMENT_MODE reason and DEFAULT_APPLICATION_SETTING_UNSUPPORTED_SCOPES
   * specific reason is reported if none of the specified scopes can be applied
   * to the management mode (e.g. a fully managed device receives a policy with
   * only SCOPE_PERSONAL_PROFILE in the list).
   *
   * @var string[]
   */
  public $defaultApplicationScopes;
  /**
   * Required. The app type to set the default application.
   *
   * @var string
   */
  public $defaultApplicationType;
  protected $defaultApplicationsType = DefaultApplication::class;
  protected $defaultApplicationsDataType = 'array';

  /**
   * Required. The scopes to which the policy should be applied. This list must
   * not be empty or contain duplicates.A NonComplianceDetail with
   * MANAGEMENT_MODE reason and DEFAULT_APPLICATION_SETTING_UNSUPPORTED_SCOPES
   * specific reason is reported if none of the specified scopes can be applied
   * to the management mode (e.g. a fully managed device receives a policy with
   * only SCOPE_PERSONAL_PROFILE in the list).
   *
   * @param string[] $defaultApplicationScopes
   */
  public function setDefaultApplicationScopes($defaultApplicationScopes)
  {
    $this->defaultApplicationScopes = $defaultApplicationScopes;
  }
  /**
   * @return string[]
   */
  public function getDefaultApplicationScopes()
  {
    return $this->defaultApplicationScopes;
  }
  /**
   * Required. The app type to set the default application.
   *
   * Accepted values: DEFAULT_APPLICATION_TYPE_UNSPECIFIED, DEFAULT_ASSISTANT,
   * DEFAULT_BROWSER, DEFAULT_CALL_REDIRECTION, DEFAULT_CALL_SCREENING,
   * DEFAULT_DIALER, DEFAULT_HOME, DEFAULT_SMS, DEFAULT_WALLET
   *
   * @param self::DEFAULT_APPLICATION_TYPE_* $defaultApplicationType
   */
  public function setDefaultApplicationType($defaultApplicationType)
  {
    $this->defaultApplicationType = $defaultApplicationType;
  }
  /**
   * @return self::DEFAULT_APPLICATION_TYPE_*
   */
  public function getDefaultApplicationType()
  {
    return $this->defaultApplicationType;
  }
  /**
   * Required. The list of applications that can be set as the default app for a
   * given type. This list must not be empty or contain duplicates. The first
   * app in the list that is installed and qualified for the
   * defaultApplicationType (e.g. SMS app for DEFAULT_SMS) is set as the default
   * app. The signing key certificate fingerprint of the app on the device must
   * also match one of the signing key certificate fingerprints obtained from
   * Play Store or one of the entries in ApplicationPolicy.signingKeyCerts in
   * order to be set as the default.If the defaultApplicationScopes contains
   * SCOPE_FULLY_MANAGED or SCOPE_WORK_PROFILE, the app must have an entry in
   * applications with installType set to a value other than BLOCKED.A
   * NonComplianceDetail with APP_NOT_INSTALLED reason and
   * DEFAULT_APPLICATION_SETTING_FAILED_FOR_SCOPE specific reason is reported if
   * none of the apps in the list are installed. A NonComplianceDetail with
   * INVALID_VALUE reason and DEFAULT_APPLICATION_SETTING_FAILED_FOR_SCOPE
   * specific reason is reported if at least one app is installed but the policy
   * fails to apply due to other reasons (e.g. the app is not of the right
   * type).When applying to SCOPE_PERSONAL_PROFILE on a company-owned device
   * with a work profile, only pre-installed system apps can be set as the
   * default. A NonComplianceDetail with INVALID_VALUE reason and
   * DEFAULT_APPLICATION_SETTING_FAILED_FOR_SCOPE specific reason is reported if
   * the policy fails to apply to the personal profile.
   *
   * @param DefaultApplication[] $defaultApplications
   */
  public function setDefaultApplications($defaultApplications)
  {
    $this->defaultApplications = $defaultApplications;
  }
  /**
   * @return DefaultApplication[]
   */
  public function getDefaultApplications()
  {
    return $this->defaultApplications;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DefaultApplicationSetting::class, 'Google_Service_AndroidManagement_DefaultApplicationSetting');
