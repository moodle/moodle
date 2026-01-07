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

class OneTimeProductPreOrderOffer extends \Google\Model
{
  /**
   * Unspecified price change behavior. Must not be used.
   */
  public const PRICE_CHANGE_BEHAVIOR_PRE_ORDER_PRICE_CHANGE_BEHAVIOR_UNSPECIFIED = 'PRE_ORDER_PRICE_CHANGE_BEHAVIOR_UNSPECIFIED';
  /**
   * The buyer gets charged the minimum between the initial price at the time of
   * pre-order and the final offer price on the release date.
   */
  public const PRICE_CHANGE_BEHAVIOR_PRE_ORDER_PRICE_CHANGE_BEHAVIOR_TWO_POINT_LOWEST = 'PRE_ORDER_PRICE_CHANGE_BEHAVIOR_TWO_POINT_LOWEST';
  /**
   * The buyer gets the same price as the one they pre-ordered, regardless of
   * any price changes that may have happened after the pre-order.
   */
  public const PRICE_CHANGE_BEHAVIOR_PRE_ORDER_PRICE_CHANGE_BEHAVIOR_NEW_ORDERS_ONLY = 'PRE_ORDER_PRICE_CHANGE_BEHAVIOR_NEW_ORDERS_ONLY';
  /**
   * Required. Time when the pre-order will stop being available.
   *
   * @var string
   */
  public $endTime;
  /**
   * Required. Immutable. Specifies how price changes affect pre-existing pre-
   * orders.
   *
   * @var string
   */
  public $priceChangeBehavior;
  /**
   * Required. Time on which the product associated with the pre-order will be
   * released and the pre-order orders fulfilled.
   *
   * @var string
   */
  public $releaseTime;
  /**
   * Required. Time when the pre-order will start being available.
   *
   * @var string
   */
  public $startTime;

  /**
   * Required. Time when the pre-order will stop being available.
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
   * Required. Immutable. Specifies how price changes affect pre-existing pre-
   * orders.
   *
   * Accepted values: PRE_ORDER_PRICE_CHANGE_BEHAVIOR_UNSPECIFIED,
   * PRE_ORDER_PRICE_CHANGE_BEHAVIOR_TWO_POINT_LOWEST,
   * PRE_ORDER_PRICE_CHANGE_BEHAVIOR_NEW_ORDERS_ONLY
   *
   * @param self::PRICE_CHANGE_BEHAVIOR_* $priceChangeBehavior
   */
  public function setPriceChangeBehavior($priceChangeBehavior)
  {
    $this->priceChangeBehavior = $priceChangeBehavior;
  }
  /**
   * @return self::PRICE_CHANGE_BEHAVIOR_*
   */
  public function getPriceChangeBehavior()
  {
    return $this->priceChangeBehavior;
  }
  /**
   * Required. Time on which the product associated with the pre-order will be
   * released and the pre-order orders fulfilled.
   *
   * @param string $releaseTime
   */
  public function setReleaseTime($releaseTime)
  {
    $this->releaseTime = $releaseTime;
  }
  /**
   * @return string
   */
  public function getReleaseTime()
  {
    return $this->releaseTime;
  }
  /**
   * Required. Time when the pre-order will start being available.
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
class_alias(OneTimeProductPreOrderOffer::class, 'Google_Service_AndroidPublisher_OneTimeProductPreOrderOffer');
