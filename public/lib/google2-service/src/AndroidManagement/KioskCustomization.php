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

class KioskCustomization extends \Google\Model
{
  /**
   * Unspecified, defaults to SETTINGS_ACCESS_ALLOWED.
   */
  public const DEVICE_SETTINGS_DEVICE_SETTINGS_UNSPECIFIED = 'DEVICE_SETTINGS_UNSPECIFIED';
  /**
   * Access to the Settings app is allowed in kiosk mode.
   */
  public const DEVICE_SETTINGS_SETTINGS_ACCESS_ALLOWED = 'SETTINGS_ACCESS_ALLOWED';
  /**
   * Access to the Settings app is not allowed in kiosk mode.
   */
  public const DEVICE_SETTINGS_SETTINGS_ACCESS_BLOCKED = 'SETTINGS_ACCESS_BLOCKED';
  /**
   * Unspecified, defaults to POWER_BUTTON_AVAILABLE.
   */
  public const POWER_BUTTON_ACTIONS_POWER_BUTTON_ACTIONS_UNSPECIFIED = 'POWER_BUTTON_ACTIONS_UNSPECIFIED';
  /**
   * The power menu (e.g. Power off, Restart) is shown when a user long-presses
   * the Power button of a device in kiosk mode.
   */
  public const POWER_BUTTON_ACTIONS_POWER_BUTTON_AVAILABLE = 'POWER_BUTTON_AVAILABLE';
  /**
   * The power menu (e.g. Power off, Restart) is not shown when a user long-
   * presses the Power button of a device in kiosk mode. Note: this may prevent
   * users from turning off the device.
   */
  public const POWER_BUTTON_ACTIONS_POWER_BUTTON_BLOCKED = 'POWER_BUTTON_BLOCKED';
  /**
   * Unspecified, defaults to INFO_AND_NOTIFICATIONS_DISABLED.
   */
  public const STATUS_BAR_STATUS_BAR_UNSPECIFIED = 'STATUS_BAR_UNSPECIFIED';
  /**
   * System info and notifications are shown on the status bar in kiosk
   * mode.Note: For this policy to take effect, the device's home button must be
   * enabled using kioskCustomization.systemNavigation.
   */
  public const STATUS_BAR_NOTIFICATIONS_AND_SYSTEM_INFO_ENABLED = 'NOTIFICATIONS_AND_SYSTEM_INFO_ENABLED';
  /**
   * System info and notifications are disabled in kiosk mode.
   */
  public const STATUS_BAR_NOTIFICATIONS_AND_SYSTEM_INFO_DISABLED = 'NOTIFICATIONS_AND_SYSTEM_INFO_DISABLED';
  /**
   * Only system info is shown on the status bar.
   */
  public const STATUS_BAR_SYSTEM_INFO_ONLY = 'SYSTEM_INFO_ONLY';
  /**
   * Unspecified, defaults to ERROR_AND_WARNINGS_MUTED.
   */
  public const SYSTEM_ERROR_WARNINGS_SYSTEM_ERROR_WARNINGS_UNSPECIFIED = 'SYSTEM_ERROR_WARNINGS_UNSPECIFIED';
  /**
   * All system error dialogs such as crash and app not responding (ANR) are
   * displayed.
   */
  public const SYSTEM_ERROR_WARNINGS_ERROR_AND_WARNINGS_ENABLED = 'ERROR_AND_WARNINGS_ENABLED';
  /**
   * All system error dialogs, such as crash and app not responding (ANR) are
   * blocked. When blocked, the system force-stops the app as if the user closes
   * the app from the UI.
   */
  public const SYSTEM_ERROR_WARNINGS_ERROR_AND_WARNINGS_MUTED = 'ERROR_AND_WARNINGS_MUTED';
  /**
   * Unspecified, defaults to NAVIGATION_DISABLED.
   */
  public const SYSTEM_NAVIGATION_SYSTEM_NAVIGATION_UNSPECIFIED = 'SYSTEM_NAVIGATION_UNSPECIFIED';
  /**
   * Home and overview buttons are enabled.
   */
  public const SYSTEM_NAVIGATION_NAVIGATION_ENABLED = 'NAVIGATION_ENABLED';
  /**
   * The home and Overview buttons are not accessible.
   */
  public const SYSTEM_NAVIGATION_NAVIGATION_DISABLED = 'NAVIGATION_DISABLED';
  /**
   * Only the home button is enabled.
   */
  public const SYSTEM_NAVIGATION_HOME_BUTTON_ONLY = 'HOME_BUTTON_ONLY';
  /**
   * Specifies whether the Settings app is allowed in kiosk mode.
   *
   * @var string
   */
  public $deviceSettings;
  /**
   * Sets the behavior of a device in kiosk mode when a user presses and holds
   * (long-presses) the Power button.
   *
   * @var string
   */
  public $powerButtonActions;
  /**
   * Specifies whether system info and notifications are disabled in kiosk mode.
   *
   * @var string
   */
  public $statusBar;
  /**
   * Specifies whether system error dialogs for crashed or unresponsive apps are
   * blocked in kiosk mode. When blocked, the system will force-stop the app as
   * if the user chooses the "close app" option on the UI.
   *
   * @var string
   */
  public $systemErrorWarnings;
  /**
   * Specifies which navigation features are enabled (e.g. Home, Overview
   * buttons) in kiosk mode.
   *
   * @var string
   */
  public $systemNavigation;

  /**
   * Specifies whether the Settings app is allowed in kiosk mode.
   *
   * Accepted values: DEVICE_SETTINGS_UNSPECIFIED, SETTINGS_ACCESS_ALLOWED,
   * SETTINGS_ACCESS_BLOCKED
   *
   * @param self::DEVICE_SETTINGS_* $deviceSettings
   */
  public function setDeviceSettings($deviceSettings)
  {
    $this->deviceSettings = $deviceSettings;
  }
  /**
   * @return self::DEVICE_SETTINGS_*
   */
  public function getDeviceSettings()
  {
    return $this->deviceSettings;
  }
  /**
   * Sets the behavior of a device in kiosk mode when a user presses and holds
   * (long-presses) the Power button.
   *
   * Accepted values: POWER_BUTTON_ACTIONS_UNSPECIFIED, POWER_BUTTON_AVAILABLE,
   * POWER_BUTTON_BLOCKED
   *
   * @param self::POWER_BUTTON_ACTIONS_* $powerButtonActions
   */
  public function setPowerButtonActions($powerButtonActions)
  {
    $this->powerButtonActions = $powerButtonActions;
  }
  /**
   * @return self::POWER_BUTTON_ACTIONS_*
   */
  public function getPowerButtonActions()
  {
    return $this->powerButtonActions;
  }
  /**
   * Specifies whether system info and notifications are disabled in kiosk mode.
   *
   * Accepted values: STATUS_BAR_UNSPECIFIED,
   * NOTIFICATIONS_AND_SYSTEM_INFO_ENABLED,
   * NOTIFICATIONS_AND_SYSTEM_INFO_DISABLED, SYSTEM_INFO_ONLY
   *
   * @param self::STATUS_BAR_* $statusBar
   */
  public function setStatusBar($statusBar)
  {
    $this->statusBar = $statusBar;
  }
  /**
   * @return self::STATUS_BAR_*
   */
  public function getStatusBar()
  {
    return $this->statusBar;
  }
  /**
   * Specifies whether system error dialogs for crashed or unresponsive apps are
   * blocked in kiosk mode. When blocked, the system will force-stop the app as
   * if the user chooses the "close app" option on the UI.
   *
   * Accepted values: SYSTEM_ERROR_WARNINGS_UNSPECIFIED,
   * ERROR_AND_WARNINGS_ENABLED, ERROR_AND_WARNINGS_MUTED
   *
   * @param self::SYSTEM_ERROR_WARNINGS_* $systemErrorWarnings
   */
  public function setSystemErrorWarnings($systemErrorWarnings)
  {
    $this->systemErrorWarnings = $systemErrorWarnings;
  }
  /**
   * @return self::SYSTEM_ERROR_WARNINGS_*
   */
  public function getSystemErrorWarnings()
  {
    return $this->systemErrorWarnings;
  }
  /**
   * Specifies which navigation features are enabled (e.g. Home, Overview
   * buttons) in kiosk mode.
   *
   * Accepted values: SYSTEM_NAVIGATION_UNSPECIFIED, NAVIGATION_ENABLED,
   * NAVIGATION_DISABLED, HOME_BUTTON_ONLY
   *
   * @param self::SYSTEM_NAVIGATION_* $systemNavigation
   */
  public function setSystemNavigation($systemNavigation)
  {
    $this->systemNavigation = $systemNavigation;
  }
  /**
   * @return self::SYSTEM_NAVIGATION_*
   */
  public function getSystemNavigation()
  {
    return $this->systemNavigation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KioskCustomization::class, 'Google_Service_AndroidManagement_KioskCustomization');
