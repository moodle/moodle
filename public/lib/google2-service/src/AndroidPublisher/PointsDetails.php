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

class PointsDetails extends \Google\Model
{
  protected $pointsCouponValueType = Money::class;
  protected $pointsCouponValueDataType = '';
  /**
   * The percentage rate which the Play Points promotion reduces the cost by.
   * E.g. for a 100 points for $2 coupon, this is 500,000. Since $2 has an
   * estimate of 200 points, but the actual Points required, 100, is 50% of
   * this, and 50% in micros is 500,000. Between 0 and 1,000,000.
   *
   * @var string
   */
  public $pointsDiscountRateMicros;
  /**
   * ID unique to the play points offer in use for this order.
   *
   * @var string
   */
  public $pointsOfferId;
  /**
   * The number of Play Points applied in this order. E.g. for a 100 points for
   * $2 coupon, this is 100. For coupon stacked with base offer, this is the
   * total points spent across both.
   *
   * @var string
   */
  public $pointsSpent;

  /**
   * The monetary value of a Play Points coupon. This is the discount the coupon
   * provides, which may not be the total amount. Only set when Play Points
   * coupons have been used. E.g. for a 100 points for $2 coupon, this is $2.
   *
   * @param Money $pointsCouponValue
   */
  public function setPointsCouponValue(Money $pointsCouponValue)
  {
    $this->pointsCouponValue = $pointsCouponValue;
  }
  /**
   * @return Money
   */
  public function getPointsCouponValue()
  {
    return $this->pointsCouponValue;
  }
  /**
   * The percentage rate which the Play Points promotion reduces the cost by.
   * E.g. for a 100 points for $2 coupon, this is 500,000. Since $2 has an
   * estimate of 200 points, but the actual Points required, 100, is 50% of
   * this, and 50% in micros is 500,000. Between 0 and 1,000,000.
   *
   * @param string $pointsDiscountRateMicros
   */
  public function setPointsDiscountRateMicros($pointsDiscountRateMicros)
  {
    $this->pointsDiscountRateMicros = $pointsDiscountRateMicros;
  }
  /**
   * @return string
   */
  public function getPointsDiscountRateMicros()
  {
    return $this->pointsDiscountRateMicros;
  }
  /**
   * ID unique to the play points offer in use for this order.
   *
   * @param string $pointsOfferId
   */
  public function setPointsOfferId($pointsOfferId)
  {
    $this->pointsOfferId = $pointsOfferId;
  }
  /**
   * @return string
   */
  public function getPointsOfferId()
  {
    return $this->pointsOfferId;
  }
  /**
   * The number of Play Points applied in this order. E.g. for a 100 points for
   * $2 coupon, this is 100. For coupon stacked with base offer, this is the
   * total points spent across both.
   *
   * @param string $pointsSpent
   */
  public function setPointsSpent($pointsSpent)
  {
    $this->pointsSpent = $pointsSpent;
  }
  /**
   * @return string
   */
  public function getPointsSpent()
  {
    return $this->pointsSpent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PointsDetails::class, 'Google_Service_AndroidPublisher_PointsDetails');
