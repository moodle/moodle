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

class AddEsimParams extends \Google\Model
{
  /**
   * eSIM activation state is not specified. This defaults to the eSIM profile
   * being NOT_ACTIVATED on personally-owned devices and ACTIVATED on company-
   * owned devices.
   */
  public const ACTIVATION_STATE_ACTIVATION_STATE_UNSPECIFIED = 'ACTIVATION_STATE_UNSPECIFIED';
  /**
   * The eSIM is automatically activated after downloading. Setting this as the
   * activation state for personally-owned devices will result in the command
   * being rejected.
   */
  public const ACTIVATION_STATE_ACTIVATED = 'ACTIVATED';
  /**
   * The eSIM profile is downloaded but not activated. In this case, the user
   * will need to activate the eSIM manually on the device.
   */
  public const ACTIVATION_STATE_NOT_ACTIVATED = 'NOT_ACTIVATED';
  /**
   * Required. The activation code for the eSIM profile.
   *
   * @var string
   */
  public $activationCode;
  /**
   * Required. The activation state of the eSIM profile once it is downloaded.
   *
   * @var string
   */
  public $activationState;

  /**
   * Required. The activation code for the eSIM profile.
   *
   * @param string $activationCode
   */
  public function setActivationCode($activationCode)
  {
    $this->activationCode = $activationCode;
  }
  /**
   * @return string
   */
  public function getActivationCode()
  {
    return $this->activationCode;
  }
  /**
   * Required. The activation state of the eSIM profile once it is downloaded.
   *
   * Accepted values: ACTIVATION_STATE_UNSPECIFIED, ACTIVATED, NOT_ACTIVATED
   *
   * @param self::ACTIVATION_STATE_* $activationState
   */
  public function setActivationState($activationState)
  {
    $this->activationState = $activationState;
  }
  /**
   * @return self::ACTIVATION_STATE_*
   */
  public function getActivationState()
  {
    return $this->activationState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddEsimParams::class, 'Google_Service_AndroidManagement_AddEsimParams');
