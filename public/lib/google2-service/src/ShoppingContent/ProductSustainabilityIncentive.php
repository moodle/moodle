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

class ProductSustainabilityIncentive extends \Google\Model
{
  /**
   * Unspecified or unknown sustainability incentive type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Program offering tax liability reductions for electric vehicles and, in
   * some countries, plug-in hybrids. These reductions can be based on a
   * specific amount or a percentage of the sale price.
   */
  public const TYPE_EV_TAX_CREDIT = 'EV_TAX_CREDIT';
  /**
   * A subsidy program, often called an environmental bonus, provides a purchase
   * grant for electric vehicles and, in some countries, plug-in hybrids. The
   * grant amount may be a fixed sum or a percentage of the sale price.
   */
  public const TYPE_EV_PRICE_DISCOUNT = 'EV_PRICE_DISCOUNT';
  protected $amountType = Price::class;
  protected $amountDataType = '';
  /**
   * Optional. The percentage of the sale price that the incentive is applied
   * to.
   *
   * @var 
   */
  public $percentage;
  /**
   * Required. Sustainability incentive program.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The fixed amount of the incentive.
   *
   * @param Price $amount
   */
  public function setAmount(Price $amount)
  {
    $this->amount = $amount;
  }
  /**
   * @return Price
   */
  public function getAmount()
  {
    return $this->amount;
  }
  public function setPercentage($percentage)
  {
    $this->percentage = $percentage;
  }
  public function getPercentage()
  {
    return $this->percentage;
  }
  /**
   * Required. Sustainability incentive program.
   *
   * Accepted values: TYPE_UNSPECIFIED, EV_TAX_CREDIT, EV_PRICE_DISCOUNT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductSustainabilityIncentive::class, 'Google_Service_ShoppingContent_ProductSustainabilityIncentive');
