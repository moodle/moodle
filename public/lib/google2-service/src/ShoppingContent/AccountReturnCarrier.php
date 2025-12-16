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

namespace Google\Service\ShoppingContent;

class AccountReturnCarrier extends \Google\Model
{
  /**
   * Carrier not specified
   */
  public const CARRIER_CODE_CARRIER_CODE_UNSPECIFIED = 'CARRIER_CODE_UNSPECIFIED';
  /**
   * FedEx carrier
   */
  public const CARRIER_CODE_FEDEX = 'FEDEX';
  /**
   * UPS carrier
   */
  public const CARRIER_CODE_UPS = 'UPS';
  /**
   * Output only. Immutable. The Google-provided unique carrier ID, used to
   * update the resource.
   *
   * @var string
   */
  public $carrierAccountId;
  /**
   * Name of the carrier account.
   *
   * @var string
   */
  public $carrierAccountName;
  /**
   * Number of the carrier account.
   *
   * @var string
   */
  public $carrierAccountNumber;
  /**
   * The carrier code enum. Accepts the values FEDEX or UPS.
   *
   * @var string
   */
  public $carrierCode;

  /**
   * Output only. Immutable. The Google-provided unique carrier ID, used to
   * update the resource.
   *
   * @param string $carrierAccountId
   */
  public function setCarrierAccountId($carrierAccountId)
  {
    $this->carrierAccountId = $carrierAccountId;
  }
  /**
   * @return string
   */
  public function getCarrierAccountId()
  {
    return $this->carrierAccountId;
  }
  /**
   * Name of the carrier account.
   *
   * @param string $carrierAccountName
   */
  public function setCarrierAccountName($carrierAccountName)
  {
    $this->carrierAccountName = $carrierAccountName;
  }
  /**
   * @return string
   */
  public function getCarrierAccountName()
  {
    return $this->carrierAccountName;
  }
  /**
   * Number of the carrier account.
   *
   * @param string $carrierAccountNumber
   */
  public function setCarrierAccountNumber($carrierAccountNumber)
  {
    $this->carrierAccountNumber = $carrierAccountNumber;
  }
  /**
   * @return string
   */
  public function getCarrierAccountNumber()
  {
    return $this->carrierAccountNumber;
  }
  /**
   * The carrier code enum. Accepts the values FEDEX or UPS.
   *
   * Accepted values: CARRIER_CODE_UNSPECIFIED, FEDEX, UPS
   *
   * @param self::CARRIER_CODE_* $carrierCode
   */
  public function setCarrierCode($carrierCode)
  {
    $this->carrierCode = $carrierCode;
  }
  /**
   * @return self::CARRIER_CODE_*
   */
  public function getCarrierCode()
  {
    return $this->carrierCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountReturnCarrier::class, 'Google_Service_ShoppingContent_AccountReturnCarrier');
