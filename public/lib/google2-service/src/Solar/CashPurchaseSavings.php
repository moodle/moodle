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

namespace Google\Service\Solar;

class CashPurchaseSavings extends \Google\Model
{
  protected $outOfPocketCostType = Money::class;
  protected $outOfPocketCostDataType = '';
  /**
   * Number of years until payback occurs. A negative value means payback never
   * occurs within the lifetime period.
   *
   * @var float
   */
  public $paybackYears;
  protected $rebateValueType = Money::class;
  protected $rebateValueDataType = '';
  protected $savingsType = SavingsOverTime::class;
  protected $savingsDataType = '';
  protected $upfrontCostType = Money::class;
  protected $upfrontCostDataType = '';

  /**
   * Initial cost before tax incentives: the amount that must be paid out-of-
   * pocket. Contrast with `upfront_cost`, which is after tax incentives.
   *
   * @param Money $outOfPocketCost
   */
  public function setOutOfPocketCost(Money $outOfPocketCost)
  {
    $this->outOfPocketCost = $outOfPocketCost;
  }
  /**
   * @return Money
   */
  public function getOutOfPocketCost()
  {
    return $this->outOfPocketCost;
  }
  /**
   * Number of years until payback occurs. A negative value means payback never
   * occurs within the lifetime period.
   *
   * @param float $paybackYears
   */
  public function setPaybackYears($paybackYears)
  {
    $this->paybackYears = $paybackYears;
  }
  /**
   * @return float
   */
  public function getPaybackYears()
  {
    return $this->paybackYears;
  }
  /**
   * The value of all tax rebates.
   *
   * @param Money $rebateValue
   */
  public function setRebateValue(Money $rebateValue)
  {
    $this->rebateValue = $rebateValue;
  }
  /**
   * @return Money
   */
  public function getRebateValue()
  {
    return $this->rebateValue;
  }
  /**
   * How much is saved (or not) over the lifetime period.
   *
   * @param SavingsOverTime $savings
   */
  public function setSavings(SavingsOverTime $savings)
  {
    $this->savings = $savings;
  }
  /**
   * @return SavingsOverTime
   */
  public function getSavings()
  {
    return $this->savings;
  }
  /**
   * Initial cost after tax incentives: it's the amount that must be paid during
   * first year. Contrast with `out_of_pocket_cost`, which is before tax
   * incentives.
   *
   * @param Money $upfrontCost
   */
  public function setUpfrontCost(Money $upfrontCost)
  {
    $this->upfrontCost = $upfrontCost;
  }
  /**
   * @return Money
   */
  public function getUpfrontCost()
  {
    return $this->upfrontCost;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CashPurchaseSavings::class, 'Google_Service_Solar_CashPurchaseSavings');
