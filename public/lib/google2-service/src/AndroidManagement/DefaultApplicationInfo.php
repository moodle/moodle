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

class DefaultApplicationInfo extends \Google\Collection
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
  protected $collection_key = 'defaultApplicationSettingAttempts';
  protected $defaultApplicationSettingAttemptsType = DefaultApplicationSettingAttempt::class;
  protected $defaultApplicationSettingAttemptsDataType = 'array';
  /**
   * Output only. The default application type.
   *
   * @var string
   */
  public $defaultApplicationType;
  /**
   * Output only. The package name of the current default application.
   *
   * @var string
   */
  public $packageName;

  /**
   * Output only. Details on the default application setting attempts, in the
   * same order as listed in defaultApplications.
   *
   * @param DefaultApplicationSettingAttempt[] $defaultApplicationSettingAttempts
   */
  public function setDefaultApplicationSettingAttempts($defaultApplicationSettingAttempts)
  {
    $this->defaultApplicationSettingAttempts = $defaultApplicationSettingAttempts;
  }
  /**
   * @return DefaultApplicationSettingAttempt[]
   */
  public function getDefaultApplicationSettingAttempts()
  {
    return $this->defaultApplicationSettingAttempts;
  }
  /**
   * Output only. The default application type.
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
   * Output only. The package name of the current default application.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DefaultApplicationInfo::class, 'Google_Service_AndroidManagement_DefaultApplicationInfo');
