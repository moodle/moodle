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

namespace Google\Service\PlayIntegrity;

class RecentDeviceActivity extends \Google\Model
{
  /**
   * Device activity level has not been set.
   */
  public const DEVICE_ACTIVITY_LEVEL_DEVICE_ACTIVITY_LEVEL_UNSPECIFIED = 'DEVICE_ACTIVITY_LEVEL_UNSPECIFIED';
  /**
   * Device activity level has not been evaluated.
   */
  public const DEVICE_ACTIVITY_LEVEL_UNEVALUATED = 'UNEVALUATED';
  /**
   * Indicates the amount of used tokens. See the documentation for details.
   */
  public const DEVICE_ACTIVITY_LEVEL_LEVEL_1 = 'LEVEL_1';
  /**
   * Indicates the amount of used tokens. See the documentation for details.
   */
  public const DEVICE_ACTIVITY_LEVEL_LEVEL_2 = 'LEVEL_2';
  /**
   * Indicates the amount of used tokens. See the documentation for details.
   */
  public const DEVICE_ACTIVITY_LEVEL_LEVEL_3 = 'LEVEL_3';
  /**
   * Indicates the amount of used tokens. See the documentation for details.
   */
  public const DEVICE_ACTIVITY_LEVEL_LEVEL_4 = 'LEVEL_4';
  /**
   * Required. Indicates the activity level of the device.
   *
   * @var string
   */
  public $deviceActivityLevel;

  /**
   * Required. Indicates the activity level of the device.
   *
   * Accepted values: DEVICE_ACTIVITY_LEVEL_UNSPECIFIED, UNEVALUATED, LEVEL_1,
   * LEVEL_2, LEVEL_3, LEVEL_4
   *
   * @param self::DEVICE_ACTIVITY_LEVEL_* $deviceActivityLevel
   */
  public function setDeviceActivityLevel($deviceActivityLevel)
  {
    $this->deviceActivityLevel = $deviceActivityLevel;
  }
  /**
   * @return self::DEVICE_ACTIVITY_LEVEL_*
   */
  public function getDeviceActivityLevel()
  {
    return $this->deviceActivityLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RecentDeviceActivity::class, 'Google_Service_PlayIntegrity_RecentDeviceActivity');
