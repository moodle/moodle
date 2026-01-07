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

class ScreenTimeoutSettings extends \Google\Model
{
  /**
   * Unspecified. Defaults to SCREEN_TIMEOUT_USER_CHOICE.
   */
  public const SCREEN_TIMEOUT_MODE_SCREEN_TIMEOUT_MODE_UNSPECIFIED = 'SCREEN_TIMEOUT_MODE_UNSPECIFIED';
  /**
   * The user is allowed to configure the screen timeout. screenTimeout must not
   * be set.
   */
  public const SCREEN_TIMEOUT_MODE_SCREEN_TIMEOUT_USER_CHOICE = 'SCREEN_TIMEOUT_USER_CHOICE';
  /**
   * The screen timeout is set to screenTimeout and the user is not allowed to
   * configure the timeout. screenTimeout must be set. Supported on Android 9
   * and above on fully managed devices. A NonComplianceDetail with API_LEVEL is
   * reported if the Android version is less than 9. Supported on work profiles
   * on company-owned devices on Android 15 and above.
   */
  public const SCREEN_TIMEOUT_MODE_SCREEN_TIMEOUT_ENFORCED = 'SCREEN_TIMEOUT_ENFORCED';
  /**
   * Optional. Controls the screen timeout duration. The screen timeout duration
   * must be greater than 0, otherwise it is rejected. Additionally, it should
   * not be greater than maximumTimeToLock, otherwise the screen timeout is set
   * to maximumTimeToLock and a NonComplianceDetail with INVALID_VALUE reason
   * and SCREEN_TIMEOUT_GREATER_THAN_MAXIMUM_TIME_TO_LOCK specific reason is
   * reported. If the screen timeout is less than a certain lower bound, it is
   * set to the lower bound. The lower bound may vary across devices. If this is
   * set, screenTimeoutMode must be SCREEN_TIMEOUT_ENFORCED. Supported on
   * Android 9 and above on fully managed devices. A NonComplianceDetail with
   * API_LEVEL is reported if the Android version is less than 9. Supported on
   * work profiles on company-owned devices on Android 15 and above.
   *
   * @var string
   */
  public $screenTimeout;
  /**
   * Optional. Controls whether the user is allowed to configure the screen
   * timeout.
   *
   * @var string
   */
  public $screenTimeoutMode;

  /**
   * Optional. Controls the screen timeout duration. The screen timeout duration
   * must be greater than 0, otherwise it is rejected. Additionally, it should
   * not be greater than maximumTimeToLock, otherwise the screen timeout is set
   * to maximumTimeToLock and a NonComplianceDetail with INVALID_VALUE reason
   * and SCREEN_TIMEOUT_GREATER_THAN_MAXIMUM_TIME_TO_LOCK specific reason is
   * reported. If the screen timeout is less than a certain lower bound, it is
   * set to the lower bound. The lower bound may vary across devices. If this is
   * set, screenTimeoutMode must be SCREEN_TIMEOUT_ENFORCED. Supported on
   * Android 9 and above on fully managed devices. A NonComplianceDetail with
   * API_LEVEL is reported if the Android version is less than 9. Supported on
   * work profiles on company-owned devices on Android 15 and above.
   *
   * @param string $screenTimeout
   */
  public function setScreenTimeout($screenTimeout)
  {
    $this->screenTimeout = $screenTimeout;
  }
  /**
   * @return string
   */
  public function getScreenTimeout()
  {
    return $this->screenTimeout;
  }
  /**
   * Optional. Controls whether the user is allowed to configure the screen
   * timeout.
   *
   * Accepted values: SCREEN_TIMEOUT_MODE_UNSPECIFIED,
   * SCREEN_TIMEOUT_USER_CHOICE, SCREEN_TIMEOUT_ENFORCED
   *
   * @param self::SCREEN_TIMEOUT_MODE_* $screenTimeoutMode
   */
  public function setScreenTimeoutMode($screenTimeoutMode)
  {
    $this->screenTimeoutMode = $screenTimeoutMode;
  }
  /**
   * @return self::SCREEN_TIMEOUT_MODE_*
   */
  public function getScreenTimeoutMode()
  {
    return $this->screenTimeoutMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScreenTimeoutSettings::class, 'Google_Service_AndroidManagement_ScreenTimeoutSettings');
