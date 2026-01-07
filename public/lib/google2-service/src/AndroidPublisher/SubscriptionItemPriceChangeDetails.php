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

class SubscriptionItemPriceChangeDetails extends \Google\Model
{
  /**
   * Price change mode unspecified. This value should never be set.
   */
  public const PRICE_CHANGE_MODE_PRICE_CHANGE_MODE_UNSPECIFIED = 'PRICE_CHANGE_MODE_UNSPECIFIED';
  /**
   * If the subscription price is decreasing.
   */
  public const PRICE_CHANGE_MODE_PRICE_DECREASE = 'PRICE_DECREASE';
  /**
   * If the subscription price is increasing and the user needs to accept it.
   */
  public const PRICE_CHANGE_MODE_PRICE_INCREASE = 'PRICE_INCREASE';
  /**
   * If the subscription price is increasing with opt out mode.
   */
  public const PRICE_CHANGE_MODE_OPT_OUT_PRICE_INCREASE = 'OPT_OUT_PRICE_INCREASE';
  /**
   * Price change state unspecified. This value should not be used.
   */
  public const PRICE_CHANGE_STATE_PRICE_CHANGE_STATE_UNSPECIFIED = 'PRICE_CHANGE_STATE_UNSPECIFIED';
  /**
   * Waiting for the user to agree for the price change.
   */
  public const PRICE_CHANGE_STATE_OUTSTANDING = 'OUTSTANDING';
  /**
   * The price change is confirmed to happen for the user.
   */
  public const PRICE_CHANGE_STATE_CONFIRMED = 'CONFIRMED';
  /**
   * The price change is applied, i.e. the user has started being charged the
   * new price.
   */
  public const PRICE_CHANGE_STATE_APPLIED = 'APPLIED';
  /**
   * The price change was canceled.
   */
  public const PRICE_CHANGE_STATE_CANCELED = 'CANCELED';
  /**
   * The renewal time at which the price change will become effective for the
   * user. This is subject to change(to a future time) due to cases where the
   * renewal time shifts like pause. This field is only populated if the price
   * change has not taken effect.
   *
   * @var string
   */
  public $expectedNewPriceChargeTime;
  protected $newPriceType = Money::class;
  protected $newPriceDataType = '';
  /**
   * Price change mode specifies how the subscription item price is changing.
   *
   * @var string
   */
  public $priceChangeMode;
  /**
   * State the price change is currently in.
   *
   * @var string
   */
  public $priceChangeState;

  /**
   * The renewal time at which the price change will become effective for the
   * user. This is subject to change(to a future time) due to cases where the
   * renewal time shifts like pause. This field is only populated if the price
   * change has not taken effect.
   *
   * @param string $expectedNewPriceChargeTime
   */
  public function setExpectedNewPriceChargeTime($expectedNewPriceChargeTime)
  {
    $this->expectedNewPriceChargeTime = $expectedNewPriceChargeTime;
  }
  /**
   * @return string
   */
  public function getExpectedNewPriceChargeTime()
  {
    return $this->expectedNewPriceChargeTime;
  }
  /**
   * New recurring price for the subscription item.
   *
   * @param Money $newPrice
   */
  public function setNewPrice(Money $newPrice)
  {
    $this->newPrice = $newPrice;
  }
  /**
   * @return Money
   */
  public function getNewPrice()
  {
    return $this->newPrice;
  }
  /**
   * Price change mode specifies how the subscription item price is changing.
   *
   * Accepted values: PRICE_CHANGE_MODE_UNSPECIFIED, PRICE_DECREASE,
   * PRICE_INCREASE, OPT_OUT_PRICE_INCREASE
   *
   * @param self::PRICE_CHANGE_MODE_* $priceChangeMode
   */
  public function setPriceChangeMode($priceChangeMode)
  {
    $this->priceChangeMode = $priceChangeMode;
  }
  /**
   * @return self::PRICE_CHANGE_MODE_*
   */
  public function getPriceChangeMode()
  {
    return $this->priceChangeMode;
  }
  /**
   * State the price change is currently in.
   *
   * Accepted values: PRICE_CHANGE_STATE_UNSPECIFIED, OUTSTANDING, CONFIRMED,
   * APPLIED, CANCELED
   *
   * @param self::PRICE_CHANGE_STATE_* $priceChangeState
   */
  public function setPriceChangeState($priceChangeState)
  {
    $this->priceChangeState = $priceChangeState;
  }
  /**
   * @return self::PRICE_CHANGE_STATE_*
   */
  public function getPriceChangeState()
  {
    return $this->priceChangeState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SubscriptionItemPriceChangeDetails::class, 'Google_Service_AndroidPublisher_SubscriptionItemPriceChangeDetails');
