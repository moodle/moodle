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

class TicketSeat extends \Google\Model
{
  public const FARE_CLASS_FARE_CLASS_UNSPECIFIED = 'FARE_CLASS_UNSPECIFIED';
  public const FARE_CLASS_ECONOMY = 'ECONOMY';
  /**
   * Legacy alias for `ECONOMY`. Deprecated.
   *
   * @deprecated
   */
  public const FARE_CLASS_economy = 'economy';
  public const FARE_CLASS_FIRST = 'FIRST';
  /**
   * Legacy alias for `FIRST`. Deprecated.
   *
   * @deprecated
   */
  public const FARE_CLASS_first = 'first';
  public const FARE_CLASS_BUSINESS = 'BUSINESS';
  /**
   * Legacy alias for `BUSINESS`. Deprecated.
   *
   * @deprecated
   */
  public const FARE_CLASS_business = 'business';
  /**
   * The identifier of the train car or coach in which the ticketed seat is
   * located. Eg. "10"
   *
   * @var string
   */
  public $coach;
  protected $customFareClassType = LocalizedString::class;
  protected $customFareClassDataType = '';
  /**
   * The fare class of the ticketed seat.
   *
   * @var string
   */
  public $fareClass;
  /**
   * The identifier of where the ticketed seat is located. Eg. "42". If there is
   * no specific identifier, use `seatAssigment` instead.
   *
   * @var string
   */
  public $seat;
  protected $seatAssignmentType = LocalizedString::class;
  protected $seatAssignmentDataType = '';

  /**
   * The identifier of the train car or coach in which the ticketed seat is
   * located. Eg. "10"
   *
   * @param string $coach
   */
  public function setCoach($coach)
  {
    $this->coach = $coach;
  }
  /**
   * @return string
   */
  public function getCoach()
  {
    return $this->coach;
  }
  /**
   * A custome fare class to be used if no `fareClass` applies. Both `fareClass`
   * and `customFareClass` may not be set.
   *
   * @param LocalizedString $customFareClass
   */
  public function setCustomFareClass(LocalizedString $customFareClass)
  {
    $this->customFareClass = $customFareClass;
  }
  /**
   * @return LocalizedString
   */
  public function getCustomFareClass()
  {
    return $this->customFareClass;
  }
  /**
   * The fare class of the ticketed seat.
   *
   * Accepted values: FARE_CLASS_UNSPECIFIED, ECONOMY, economy, FIRST, first,
   * BUSINESS, business
   *
   * @param self::FARE_CLASS_* $fareClass
   */
  public function setFareClass($fareClass)
  {
    $this->fareClass = $fareClass;
  }
  /**
   * @return self::FARE_CLASS_*
   */
  public function getFareClass()
  {
    return $this->fareClass;
  }
  /**
   * The identifier of where the ticketed seat is located. Eg. "42". If there is
   * no specific identifier, use `seatAssigment` instead.
   *
   * @param string $seat
   */
  public function setSeat($seat)
  {
    $this->seat = $seat;
  }
  /**
   * @return string
   */
  public function getSeat()
  {
    return $this->seat;
  }
  /**
   * The passenger's seat assignment. Eg. "no specific seat". To be used when
   * there is no specific identifier to use in `seat`.
   *
   * @param LocalizedString $seatAssignment
   */
  public function setSeatAssignment(LocalizedString $seatAssignment)
  {
    $this->seatAssignment = $seatAssignment;
  }
  /**
   * @return LocalizedString
   */
  public function getSeatAssignment()
  {
    return $this->seatAssignment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TicketSeat::class, 'Google_Service_Walletobjects_TicketSeat');
