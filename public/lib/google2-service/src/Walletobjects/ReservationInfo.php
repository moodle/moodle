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

namespace Google\Service\Walletobjects;

class ReservationInfo extends \Google\Model
{
  /**
   * Confirmation code needed to check into this flight. This is the number that
   * the passenger would enter into a kiosk at the airport to look up the flight
   * and print a boarding pass.
   *
   * @var string
   */
  public $confirmationCode;
  /**
   * E-ticket number.
   *
   * @var string
   */
  public $eticketNumber;
  protected $frequentFlyerInfoType = FrequentFlyerInfo::class;
  protected $frequentFlyerInfoDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#reservationInfo"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;

  /**
   * Confirmation code needed to check into this flight. This is the number that
   * the passenger would enter into a kiosk at the airport to look up the flight
   * and print a boarding pass.
   *
   * @param string $confirmationCode
   */
  public function setConfirmationCode($confirmationCode)
  {
    $this->confirmationCode = $confirmationCode;
  }
  /**
   * @return string
   */
  public function getConfirmationCode()
  {
    return $this->confirmationCode;
  }
  /**
   * E-ticket number.
   *
   * @param string $eticketNumber
   */
  public function setEticketNumber($eticketNumber)
  {
    $this->eticketNumber = $eticketNumber;
  }
  /**
   * @return string
   */
  public function getEticketNumber()
  {
    return $this->eticketNumber;
  }
  /**
   * Frequent flyer membership information.
   *
   * @param FrequentFlyerInfo $frequentFlyerInfo
   */
  public function setFrequentFlyerInfo(FrequentFlyerInfo $frequentFlyerInfo)
  {
    $this->frequentFlyerInfo = $frequentFlyerInfo;
  }
  /**
   * @return FrequentFlyerInfo
   */
  public function getFrequentFlyerInfo()
  {
    return $this->frequentFlyerInfo;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#reservationInfo"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReservationInfo::class, 'Google_Service_Walletobjects_ReservationInfo');
