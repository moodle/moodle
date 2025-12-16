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

class ScreenBrightnessSettings extends \Google\Model
{
  /**
   * Unspecified. Defaults to BRIGHTNESS_USER_CHOICE.
   */
  public const SCREEN_BRIGHTNESS_MODE_SCREEN_BRIGHTNESS_MODE_UNSPECIFIED = 'SCREEN_BRIGHTNESS_MODE_UNSPECIFIED';
  /**
   * The user is allowed to configure the screen brightness. screenBrightness
   * must not be set.
   */
  public const SCREEN_BRIGHTNESS_MODE_BRIGHTNESS_USER_CHOICE = 'BRIGHTNESS_USER_CHOICE';
  /**
   * The screen brightness mode is automatic in which the brightness is
   * automatically adjusted and the user is not allowed to configure the screen
   * brightness. screenBrightness can still be set and it is taken into account
   * while the brightness is automatically adjusted. Supported on Android 9 and
   * above on fully managed devices. A NonComplianceDetail with API_LEVEL is
   * reported if the Android version is less than 9. Supported on work profiles
   * on company-owned devices on Android 15 and above.
   */
  public const SCREEN_BRIGHTNESS_MODE_BRIGHTNESS_AUTOMATIC = 'BRIGHTNESS_AUTOMATIC';
  /**
   * The screen brightness mode is fixed in which the brightness is set to
   * screenBrightness and the user is not allowed to configure the screen
   * brightness. screenBrightness must be set. Supported on Android 9 and above
   * on fully managed devices. A NonComplianceDetail with API_LEVEL is reported
   * if the Android version is less than 9. Supported on work profiles on
   * company-owned devices on Android 15 and above.
   */
  public const SCREEN_BRIGHTNESS_MODE_BRIGHTNESS_FIXED = 'BRIGHTNESS_FIXED';
  /**
   * Optional. The screen brightness between 1 and 255 where 1 is the lowest and
   * 255 is the highest brightness. A value of 0 (default) means no screen
   * brightness set. Any other value is rejected. screenBrightnessMode must be
   * either BRIGHTNESS_AUTOMATIC or BRIGHTNESS_FIXED to set this. Supported on
   * Android 9 and above on fully managed devices. A NonComplianceDetail with
   * API_LEVEL is reported if the Android version is less than 9. Supported on
   * work profiles on company-owned devices on Android 15 and above.
   *
   * @var int
   */
  public $screenBrightness;
  /**
   * Optional. Controls the screen brightness mode.
   *
   * @var string
   */
  public $screenBrightnessMode;

  /**
   * Optional. The screen brightness between 1 and 255 where 1 is the lowest and
   * 255 is the highest brightness. A value of 0 (default) means no screen
   * brightness set. Any other value is rejected. screenBrightnessMode must be
   * either BRIGHTNESS_AUTOMATIC or BRIGHTNESS_FIXED to set this. Supported on
   * Android 9 and above on fully managed devices. A NonComplianceDetail with
   * API_LEVEL is reported if the Android version is less than 9. Supported on
   * work profiles on company-owned devices on Android 15 and above.
   *
   * @param int $screenBrightness
   */
  public function setScreenBrightness($screenBrightness)
  {
    $this->screenBrightness = $screenBrightness;
  }
  /**
   * @return int
   */
  public function getScreenBrightness()
  {
    return $this->screenBrightness;
  }
  /**
   * Optional. Controls the screen brightness mode.
   *
   * Accepted values: SCREEN_BRIGHTNESS_MODE_UNSPECIFIED,
   * BRIGHTNESS_USER_CHOICE, BRIGHTNESS_AUTOMATIC, BRIGHTNESS_FIXED
   *
   * @param self::SCREEN_BRIGHTNESS_MODE_* $screenBrightnessMode
   */
  public function setScreenBrightnessMode($screenBrightnessMode)
  {
    $this->screenBrightnessMode = $screenBrightnessMode;
  }
  /**
   * @return self::SCREEN_BRIGHTNESS_MODE_*
   */
  public function getScreenBrightnessMode()
  {
    return $this->screenBrightnessMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScreenBrightnessSettings::class, 'Google_Service_AndroidManagement_ScreenBrightnessSettings');
