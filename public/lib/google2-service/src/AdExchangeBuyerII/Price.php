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

namespace Google\Service\AdExchangeBuyerII;

class Price extends \Google\Model
{
  /**
   * A placeholder for an undefined pricing type. If the pricing type is
   * unspecified, `COST_PER_MILLE` will be used instead.
   */
  public const PRICING_TYPE_PRICING_TYPE_UNSPECIFIED = 'PRICING_TYPE_UNSPECIFIED';
  /**
   * Cost per thousand impressions.
   */
  public const PRICING_TYPE_COST_PER_MILLE = 'COST_PER_MILLE';
  /**
   * Cost per day
   */
  public const PRICING_TYPE_COST_PER_DAY = 'COST_PER_DAY';
  protected $amountType = Money::class;
  protected $amountDataType = '';
  /**
   * The pricing type for the deal/product. (default: CPM)
   *
   * @var string
   */
  public $pricingType;

  /**
   * The actual price with currency specified.
   *
   * @param Money $amount
   */
  public function setAmount(Money $amount)
  {
    $this->amount = $amount;
  }
  /**
   * @return Money
   */
  public function getAmount()
  {
    return $this->amount;
  }
  /**
   * The pricing type for the deal/product. (default: CPM)
   *
   * Accepted values: PRICING_TYPE_UNSPECIFIED, COST_PER_MILLE, COST_PER_DAY
   *
   * @param self::PRICING_TYPE_* $pricingType
   */
  public function setPricingType($pricingType)
  {
    $this->pricingType = $pricingType;
  }
  /**
   * @return self::PRICING_TYPE_*
   */
  public function getPricingType()
  {
    return $this->pricingType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Price::class, 'Google_Service_AdExchangeBuyerII_Price');
