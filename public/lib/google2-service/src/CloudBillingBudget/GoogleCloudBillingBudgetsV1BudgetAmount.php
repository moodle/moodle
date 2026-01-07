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

namespace Google\Service\CloudBillingBudget;

class GoogleCloudBillingBudgetsV1BudgetAmount extends \Google\Model
{
  protected $lastPeriodAmountType = GoogleCloudBillingBudgetsV1LastPeriodAmount::class;
  protected $lastPeriodAmountDataType = '';
  protected $specifiedAmountType = GoogleTypeMoney::class;
  protected $specifiedAmountDataType = '';

  /**
   * Use the last period's actual spend as the budget for the present period.
   * LastPeriodAmount can only be set when the budget's time period is a
   * Filter.calendar_period. It cannot be set in combination with
   * Filter.custom_period.
   *
   * @param GoogleCloudBillingBudgetsV1LastPeriodAmount $lastPeriodAmount
   */
  public function setLastPeriodAmount(GoogleCloudBillingBudgetsV1LastPeriodAmount $lastPeriodAmount)
  {
    $this->lastPeriodAmount = $lastPeriodAmount;
  }
  /**
   * @return GoogleCloudBillingBudgetsV1LastPeriodAmount
   */
  public function getLastPeriodAmount()
  {
    return $this->lastPeriodAmount;
  }
  /**
   * A specified amount to use as the budget. `currency_code` is optional. If
   * specified when creating a budget, it must match the currency of the billing
   * account. If specified when updating a budget, it must match the
   * currency_code of the existing budget. The `currency_code` is provided on
   * output.
   *
   * @param GoogleTypeMoney $specifiedAmount
   */
  public function setSpecifiedAmount(GoogleTypeMoney $specifiedAmount)
  {
    $this->specifiedAmount = $specifiedAmount;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getSpecifiedAmount()
  {
    return $this->specifiedAmount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBillingBudgetsV1BudgetAmount::class, 'Google_Service_CloudBillingBudget_GoogleCloudBillingBudgetsV1BudgetAmount');
