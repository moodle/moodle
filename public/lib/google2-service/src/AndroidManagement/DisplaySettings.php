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

class DisplaySettings extends \Google\Model
{
  protected $screenBrightnessSettingsType = ScreenBrightnessSettings::class;
  protected $screenBrightnessSettingsDataType = '';
  protected $screenTimeoutSettingsType = ScreenTimeoutSettings::class;
  protected $screenTimeoutSettingsDataType = '';

  /**
   * Optional. Controls the screen brightness settings.
   *
   * @param ScreenBrightnessSettings $screenBrightnessSettings
   */
  public function setScreenBrightnessSettings(ScreenBrightnessSettings $screenBrightnessSettings)
  {
    $this->screenBrightnessSettings = $screenBrightnessSettings;
  }
  /**
   * @return ScreenBrightnessSettings
   */
  public function getScreenBrightnessSettings()
  {
    return $this->screenBrightnessSettings;
  }
  /**
   * Optional. Controls the screen timeout settings.
   *
   * @param ScreenTimeoutSettings $screenTimeoutSettings
   */
  public function setScreenTimeoutSettings(ScreenTimeoutSettings $screenTimeoutSettings)
  {
    $this->screenTimeoutSettings = $screenTimeoutSettings;
  }
  /**
   * @return ScreenTimeoutSettings
   */
  public function getScreenTimeoutSettings()
  {
    return $this->screenTimeoutSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DisplaySettings::class, 'Google_Service_AndroidManagement_DisplaySettings');
