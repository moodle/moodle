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

namespace Google\Service\AndroidPublisher;

class OneTimeProductDiscountedOffer extends \Google\Model
{
  /**
   * Time when the offer will stop being available.
   *
   * @var string
   */
  public $endTime;
  /**
   * Optional. The number of times this offer can be redeemed. If unset or set
   * to 0, allows for unlimited offer redemptions. Otherwise must be a number
   * between 1 and 50 inclusive.
   *
   * @var string
   */
  public $redemptionLimit;
  /**
   * Time when the offer will start being available.
   *
   * @var string
   */
  public $startTime;

  /**
   * Time when the offer will stop being available.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Optional. The number of times this offer can be redeemed. If unset or set
   * to 0, allows for unlimited offer redemptions. Otherwise must be a number
   * between 1 and 50 inclusive.
   *
   * @param string $redemptionLimit
   */
  public function setRedemptionLimit($redemptionLimit)
  {
    $this->redemptionLimit = $redemptionLimit;
  }
  /**
   * @return string
   */
  public function getRedemptionLimit()
  {
    return $this->redemptionLimit;
  }
  /**
   * Time when the offer will start being available.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OneTimeProductDiscountedOffer::class, 'Google_Service_AndroidPublisher_OneTimeProductDiscountedOffer');
