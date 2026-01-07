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

class ProductSubscriptionCost extends \Google\Model
{
  protected $amountType = Price::class;
  protected $amountDataType = '';
  /**
   * The type of subscription period. - "`month`" - "`year`"
   *
   * @var string
   */
  public $period;
  /**
   * The number of subscription periods the buyer has to pay.
   *
   * @var string
   */
  public $periodLength;

  /**
   * The amount the buyer has to pay per subscription period.
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
  /**
   * The type of subscription period. - "`month`" - "`year`"
   *
   * @param string $period
   */
  public function setPeriod($period)
  {
    $this->period = $period;
  }
  /**
   * @return string
   */
  public function getPeriod()
  {
    return $this->period;
  }
  /**
   * The number of subscription periods the buyer has to pay.
   *
   * @param string $periodLength
   */
  public function setPeriodLength($periodLength)
  {
    $this->periodLength = $periodLength;
  }
  /**
   * @return string
   */
  public function getPeriodLength()
  {
    return $this->periodLength;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductSubscriptionCost::class, 'Google_Service_ShoppingContent_ProductSubscriptionCost');
