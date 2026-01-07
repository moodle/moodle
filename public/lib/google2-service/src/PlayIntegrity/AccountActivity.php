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

class AccountActivity extends \Google\Model
{
  /**
   * Activity level has not been set.
   */
  public const ACTIVITY_LEVEL_ACTIVITY_LEVEL_UNSPECIFIED = 'ACTIVITY_LEVEL_UNSPECIFIED';
  /**
   * Account activity level is not evaluated.
   */
  public const ACTIVITY_LEVEL_UNEVALUATED = 'UNEVALUATED';
  /**
   * Unusual activity for at least one of the user accounts on the device.
   */
  public const ACTIVITY_LEVEL_UNUSUAL = 'UNUSUAL';
  /**
   * Insufficient activity to verify the user account on the device.
   */
  public const ACTIVITY_LEVEL_UNKNOWN = 'UNKNOWN';
  /**
   * Typical activity for the user account or accounts on the device.
   */
  public const ACTIVITY_LEVEL_TYPICAL_BASIC = 'TYPICAL_BASIC';
  /**
   * Typical for the user account or accounts on the device, with harder to
   * replicate signals.
   */
  public const ACTIVITY_LEVEL_TYPICAL_STRONG = 'TYPICAL_STRONG';
  /**
   * Required. Indicates the activity level of the account.
   *
   * @var string
   */
  public $activityLevel;

  /**
   * Required. Indicates the activity level of the account.
   *
   * Accepted values: ACTIVITY_LEVEL_UNSPECIFIED, UNEVALUATED, UNUSUAL, UNKNOWN,
   * TYPICAL_BASIC, TYPICAL_STRONG
   *
   * @param self::ACTIVITY_LEVEL_* $activityLevel
   */
  public function setActivityLevel($activityLevel)
  {
    $this->activityLevel = $activityLevel;
  }
  /**
   * @return self::ACTIVITY_LEVEL_*
   */
  public function getActivityLevel()
  {
    return $this->activityLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountActivity::class, 'Google_Service_PlayIntegrity_AccountActivity');
