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

namespace Google\Service\Css;

class HeadlineOfferSubscriptionCost extends \Google\Model
{
  /**
   * Indicates that the subscription period is unspecified.
   */
  public const PERIOD_SUBSCRIPTION_PERIOD_UNSPECIFIED = 'SUBSCRIPTION_PERIOD_UNSPECIFIED';
  /**
   * Indicates that the subscription period is month.
   */
  public const PERIOD_MONTH = 'MONTH';
  /**
   * Indicates that the subscription period is year.
   */
  public const PERIOD_YEAR = 'YEAR';
  protected $amountType = Price::class;
  protected $amountDataType = '';
  /**
   * The type of subscription period. Supported values are: * "`month`" *
   * "`year`"
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
   * The type of subscription period. Supported values are: * "`month`" *
   * "`year`"
   *
   * Accepted values: SUBSCRIPTION_PERIOD_UNSPECIFIED, MONTH, YEAR
   *
   * @param self::PERIOD_* $period
   */
  public function setPeriod($period)
  {
    $this->period = $period;
  }
  /**
   * @return self::PERIOD_*
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
class_alias(HeadlineOfferSubscriptionCost::class, 'Google_Service_Css_HeadlineOfferSubscriptionCost');
