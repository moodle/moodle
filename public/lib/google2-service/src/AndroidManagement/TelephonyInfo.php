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

class TelephonyInfo extends \Google\Model
{
  /**
   * Activation state is not specified.
   */
  public const ACTIVATION_STATE_ACTIVATION_STATE_UNSPECIFIED = 'ACTIVATION_STATE_UNSPECIFIED';
  /**
   * The SIM card is activated.
   */
  public const ACTIVATION_STATE_ACTIVATED = 'ACTIVATED';
  /**
   * The SIM card is not activated.
   */
  public const ACTIVATION_STATE_NOT_ACTIVATED = 'NOT_ACTIVATED';
  /**
   * The configuration mode is unspecified.
   */
  public const CONFIG_MODE_CONFIG_MODE_UNSPECIFIED = 'CONFIG_MODE_UNSPECIFIED';
  /**
   * The admin has configured this SIM.
   */
  public const CONFIG_MODE_ADMIN_CONFIGURED = 'ADMIN_CONFIGURED';
  /**
   * The user has configured this SIM.
   */
  public const CONFIG_MODE_USER_CONFIGURED = 'USER_CONFIGURED';
  /**
   * Output only. Activation state of the SIM card on the device. This is
   * applicable for eSIMs only. This is supported on all devices for API level
   * 35 and above. This is always ACTIVATION_STATE_UNSPECIFIED for physical SIMs
   * and for devices below API level 35.
   *
   * @var string
   */
  public $activationState;
  /**
   * The carrier name associated with this SIM card.
   *
   * @var string
   */
  public $carrierName;
  /**
   * Output only. The configuration mode of the SIM card on the device. This is
   * applicable for eSIMs only. This is supported on all devices for API level
   * 35 and above. This is always CONFIG_MODE_UNSPECIFIED for physical SIMs and
   * for devices below API level 35.
   *
   * @var string
   */
  public $configMode;
  /**
   * Output only. The ICCID associated with this SIM card.
   *
   * @var string
   */
  public $iccId;
  /**
   * The phone number associated with this SIM card.
   *
   * @var string
   */
  public $phoneNumber;

  /**
   * Output only. Activation state of the SIM card on the device. This is
   * applicable for eSIMs only. This is supported on all devices for API level
   * 35 and above. This is always ACTIVATION_STATE_UNSPECIFIED for physical SIMs
   * and for devices below API level 35.
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
  /**
   * The carrier name associated with this SIM card.
   *
   * @param string $carrierName
   */
  public function setCarrierName($carrierName)
  {
    $this->carrierName = $carrierName;
  }
  /**
   * @return string
   */
  public function getCarrierName()
  {
    return $this->carrierName;
  }
  /**
   * Output only. The configuration mode of the SIM card on the device. This is
   * applicable for eSIMs only. This is supported on all devices for API level
   * 35 and above. This is always CONFIG_MODE_UNSPECIFIED for physical SIMs and
   * for devices below API level 35.
   *
   * Accepted values: CONFIG_MODE_UNSPECIFIED, ADMIN_CONFIGURED, USER_CONFIGURED
   *
   * @param self::CONFIG_MODE_* $configMode
   */
  public function setConfigMode($configMode)
  {
    $this->configMode = $configMode;
  }
  /**
   * @return self::CONFIG_MODE_*
   */
  public function getConfigMode()
  {
    return $this->configMode;
  }
  /**
   * Output only. The ICCID associated with this SIM card.
   *
   * @param string $iccId
   */
  public function setIccId($iccId)
  {
    $this->iccId = $iccId;
  }
  /**
   * @return string
   */
  public function getIccId()
  {
    return $this->iccId;
  }
  /**
   * The phone number associated with this SIM card.
   *
   * @param string $phoneNumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }
  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TelephonyInfo::class, 'Google_Service_AndroidManagement_TelephonyInfo');
