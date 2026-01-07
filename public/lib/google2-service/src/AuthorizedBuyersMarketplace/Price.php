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

namespace Google\Service\AuthorizedBuyersMarketplace;

class Price extends \Google\Model
{
  /**
   * A placeholder for an undefined pricing type. If the pricing type is
   * unspecified, CPM will be used instead.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Cost per thousand impressions.
   */
  public const TYPE_CPM = 'CPM';
  /**
   * Cost per day.
   */
  public const TYPE_CPD = 'CPD';
  protected $amountType = Money::class;
  protected $amountDataType = '';
  /**
   * The pricing type for the deal.
   *
   * @var string
   */
  public $type;

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
   * The pricing type for the deal.
   *
   * Accepted values: TYPE_UNSPECIFIED, CPM, CPD
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
class_alias(Price::class, 'Google_Service_AuthorizedBuyersMarketplace_Price');
