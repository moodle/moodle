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

namespace Google\Service\AndroidEnterprise;

class DeviceState extends \Google\Model
{
  public const ACCOUNT_STATE_enabled = 'enabled';
  public const ACCOUNT_STATE_disabled = 'disabled';
  /**
   * The state of the Google account on the device. "enabled" indicates that the
   * Google account on the device can be used to access Google services
   * (including Google Play), while "disabled" means that it cannot. A new
   * device is initially in the "disabled" state.
   *
   * @var string
   */
  public $accountState;

  /**
   * The state of the Google account on the device. "enabled" indicates that the
   * Google account on the device can be used to access Google services
   * (including Google Play), while "disabled" means that it cannot. A new
   * device is initially in the "disabled" state.
   *
   * Accepted values: enabled, disabled
   *
   * @param self::ACCOUNT_STATE_* $accountState
   */
  public function setAccountState($accountState)
  {
    $this->accountState = $accountState;
  }
  /**
   * @return self::ACCOUNT_STATE_*
   */
  public function getAccountState()
  {
    return $this->accountState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceState::class, 'Google_Service_AndroidEnterprise_DeviceState');
