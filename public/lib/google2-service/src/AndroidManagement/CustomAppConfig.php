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

class CustomAppConfig extends \Google\Model
{
  /**
   * Unspecified. Defaults to DISALLOW_UNINSTALL_BY_USER.
   */
  public const USER_UNINSTALL_SETTINGS_USER_UNINSTALL_SETTINGS_UNSPECIFIED = 'USER_UNINSTALL_SETTINGS_UNSPECIFIED';
  /**
   * User is not allowed to uninstall the custom app.
   */
  public const USER_UNINSTALL_SETTINGS_DISALLOW_UNINSTALL_BY_USER = 'DISALLOW_UNINSTALL_BY_USER';
  /**
   * User is allowed to uninstall the custom app.
   */
  public const USER_UNINSTALL_SETTINGS_ALLOW_UNINSTALL_BY_USER = 'ALLOW_UNINSTALL_BY_USER';
  /**
   * Optional. User uninstall settings of the custom app.
   *
   * @var string
   */
  public $userUninstallSettings;

  /**
   * Optional. User uninstall settings of the custom app.
   *
   * Accepted values: USER_UNINSTALL_SETTINGS_UNSPECIFIED,
   * DISALLOW_UNINSTALL_BY_USER, ALLOW_UNINSTALL_BY_USER
   *
   * @param self::USER_UNINSTALL_SETTINGS_* $userUninstallSettings
   */
  public function setUserUninstallSettings($userUninstallSettings)
  {
    $this->userUninstallSettings = $userUninstallSettings;
  }
  /**
   * @return self::USER_UNINSTALL_SETTINGS_*
   */
  public function getUserUninstallSettings()
  {
    return $this->userUninstallSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomAppConfig::class, 'Google_Service_AndroidManagement_CustomAppConfig');
