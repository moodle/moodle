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

class LoyaltyProgram extends \Google\Model
{
  protected $cashbackForFutureUseType = Price::class;
  protected $cashbackForFutureUseDataType = '';
  /**
   * Optional. The amount of loyalty points earned on a purchase.
   *
   * @var string
   */
  public $loyaltyPoints;
  /**
   * Optional. A date range during which the item is eligible for member price.
   * If not specified, the member price is always applicable. The date range is
   * represented by a pair of ISO 8601 dates separated by a space, comma, or
   * slash.
   *
   * @var string
   */
  public $memberPriceEffectiveDate;
  protected $priceType = Price::class;
  protected $priceDataType = '';
  /**
   * Required. The label of the loyalty program. This is an internal label that
   * uniquely identifies the relationship between a merchant entity and a
   * loyalty program entity. It must be provided so that system can associate
   * the assets below (for example, price and points) with a merchant. The
   * corresponding program must be linked to the merchant account.
   *
   * @var string
   */
  public $programLabel;
  /**
   * Optional. The shipping label for the loyalty program. You can use this
   * label to indicate whether this offer has the loyalty shipping benefit. If
   * not specified, the item is not eligible for loyalty shipping for the given
   * loyalty tier.
   *
   * @var string
   */
  public $shippingLabel;
  /**
   * Required. The label of the tier within the loyalty program. Must match one
   * of the labels within the program.
   *
   * @var string
   */
  public $tierLabel;

  /**
   * Optional. The cashback that can be used for future purchases.
   *
   * @param Price $cashbackForFutureUse
   */
  public function setCashbackForFutureUse(Price $cashbackForFutureUse)
  {
    $this->cashbackForFutureUse = $cashbackForFutureUse;
  }
  /**
   * @return Price
   */
  public function getCashbackForFutureUse()
  {
    return $this->cashbackForFutureUse;
  }
  /**
   * Optional. The amount of loyalty points earned on a purchase.
   *
   * @param string $loyaltyPoints
   */
  public function setLoyaltyPoints($loyaltyPoints)
  {
    $this->loyaltyPoints = $loyaltyPoints;
  }
  /**
   * @return string
   */
  public function getLoyaltyPoints()
  {
    return $this->loyaltyPoints;
  }
  /**
   * Optional. A date range during which the item is eligible for member price.
   * If not specified, the member price is always applicable. The date range is
   * represented by a pair of ISO 8601 dates separated by a space, comma, or
   * slash.
   *
   * @param string $memberPriceEffectiveDate
   */
  public function setMemberPriceEffectiveDate($memberPriceEffectiveDate)
  {
    $this->memberPriceEffectiveDate = $memberPriceEffectiveDate;
  }
  /**
   * @return string
   */
  public function getMemberPriceEffectiveDate()
  {
    return $this->memberPriceEffectiveDate;
  }
  /**
   * Optional. The price for members of the given tier (instant discount price).
   * Must be smaller or equal to the regular price.
   *
   * @param Price $price
   */
  public function setPrice(Price $price)
  {
    $this->price = $price;
  }
  /**
   * @return Price
   */
  public function getPrice()
  {
    return $this->price;
  }
  /**
   * Required. The label of the loyalty program. This is an internal label that
   * uniquely identifies the relationship between a merchant entity and a
   * loyalty program entity. It must be provided so that system can associate
   * the assets below (for example, price and points) with a merchant. The
   * corresponding program must be linked to the merchant account.
   *
   * @param string $programLabel
   */
  public function setProgramLabel($programLabel)
  {
    $this->programLabel = $programLabel;
  }
  /**
   * @return string
   */
  public function getProgramLabel()
  {
    return $this->programLabel;
  }
  /**
   * Optional. The shipping label for the loyalty program. You can use this
   * label to indicate whether this offer has the loyalty shipping benefit. If
   * not specified, the item is not eligible for loyalty shipping for the given
   * loyalty tier.
   *
   * @param string $shippingLabel
   */
  public function setShippingLabel($shippingLabel)
  {
    $this->shippingLabel = $shippingLabel;
  }
  /**
   * @return string
   */
  public function getShippingLabel()
  {
    return $this->shippingLabel;
  }
  /**
   * Required. The label of the tier within the loyalty program. Must match one
   * of the labels within the program.
   *
   * @param string $tierLabel
   */
  public function setTierLabel($tierLabel)
  {
    $this->tierLabel = $tierLabel;
  }
  /**
   * @return string
   */
  public function getTierLabel()
  {
    return $this->tierLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoyaltyProgram::class, 'Google_Service_ShoppingContent_LoyaltyProgram');
