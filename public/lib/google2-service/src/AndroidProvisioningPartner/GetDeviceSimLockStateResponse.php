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

namespace Google\Service\AndroidProvisioningPartner;

class GetDeviceSimLockStateResponse extends \Google\Model
{
  /**
   * Invalid code. Shouldn't be used.
   */
  public const SIM_LOCK_STATE_SIM_LOCK_STATE_UNSPECIFIED = 'SIM_LOCK_STATE_UNSPECIFIED';
  /**
   * Device is not SIM locked.
   */
  public const SIM_LOCK_STATE_UNLOCKED = 'UNLOCKED';
  /**
   * Device is SIM locked to the partner querying SIM lock state.
   */
  public const SIM_LOCK_STATE_LOCKED_TO_PARTNER = 'LOCKED_TO_PARTNER';
  /**
   * Device is SIM locked to a different partner.
   */
  public const SIM_LOCK_STATE_LOCKED_TO_OTHER_PARTNER = 'LOCKED_TO_OTHER_PARTNER';
  /**
   * @var string
   */
  public $simLockState;

  /**
   * @param self::SIM_LOCK_STATE_* $simLockState
   */
  public function setSimLockState($simLockState)
  {
    $this->simLockState = $simLockState;
  }
  /**
   * @return self::SIM_LOCK_STATE_*
   */
  public function getSimLockState()
  {
    return $this->simLockState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetDeviceSimLockStateResponse::class, 'Google_Service_AndroidProvisioningPartner_GetDeviceSimLockStateResponse');
