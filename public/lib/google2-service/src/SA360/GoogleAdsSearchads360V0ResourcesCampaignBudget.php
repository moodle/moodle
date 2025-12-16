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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesCampaignBudget extends \Google\Model
{
  /**
   * Not specified.
   */
  public const DELIVERY_METHOD_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const DELIVERY_METHOD_UNKNOWN = 'UNKNOWN';
  /**
   * The budget server will throttle serving evenly across the entire time
   * period.
   */
  public const DELIVERY_METHOD_STANDARD = 'STANDARD';
  /**
   * The budget server will not throttle serving, and ads will serve as fast as
   * possible.
   */
  public const DELIVERY_METHOD_ACCELERATED = 'ACCELERATED';
  /**
   * Not specified.
   */
  public const PERIOD_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const PERIOD_UNKNOWN = 'UNKNOWN';
  /**
   * Daily budget.
   */
  public const PERIOD_DAILY = 'DAILY';
  /**
   * Fixed daily budget.
   */
  public const PERIOD_FIXED_DAILY = 'FIXED_DAILY';
  /**
   * Custom budget can be used with total_amount to specify lifetime budget
   * limit.
   */
  public const PERIOD_CUSTOM_PERIOD = 'CUSTOM_PERIOD';
  /**
   * The amount of the budget, in the local currency for the account. Amount is
   * specified in micros, where one million is equivalent to one currency unit.
   * Monthly spend is capped at 30.4 times this amount.
   *
   * @var string
   */
  public $amountMicros;
  /**
   * The delivery method that determines the rate at which the campaign budget
   * is spent. Defaults to STANDARD if unspecified in a create operation.
   *
   * @var string
   */
  public $deliveryMethod;
  /**
   * Immutable. Period over which to spend the budget. Defaults to DAILY if not
   * specified.
   *
   * @var string
   */
  public $period;
  /**
   * Immutable. The resource name of the campaign budget. Campaign budget
   * resource names have the form:
   * `customers/{customer_id}/campaignBudgets/{campaign_budget_id}`
   *
   * @var string
   */
  public $resourceName;

  /**
   * The amount of the budget, in the local currency for the account. Amount is
   * specified in micros, where one million is equivalent to one currency unit.
   * Monthly spend is capped at 30.4 times this amount.
   *
   * @param string $amountMicros
   */
  public function setAmountMicros($amountMicros)
  {
    $this->amountMicros = $amountMicros;
  }
  /**
   * @return string
   */
  public function getAmountMicros()
  {
    return $this->amountMicros;
  }
  /**
   * The delivery method that determines the rate at which the campaign budget
   * is spent. Defaults to STANDARD if unspecified in a create operation.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, STANDARD, ACCELERATED
   *
   * @param self::DELIVERY_METHOD_* $deliveryMethod
   */
  public function setDeliveryMethod($deliveryMethod)
  {
    $this->deliveryMethod = $deliveryMethod;
  }
  /**
   * @return self::DELIVERY_METHOD_*
   */
  public function getDeliveryMethod()
  {
    return $this->deliveryMethod;
  }
  /**
   * Immutable. Period over which to spend the budget. Defaults to DAILY if not
   * specified.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, DAILY, FIXED_DAILY, CUSTOM_PERIOD
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
   * Immutable. The resource name of the campaign budget. Campaign budget
   * resource names have the form:
   * `customers/{customer_id}/campaignBudgets/{campaign_budget_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesCampaignBudget::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesCampaignBudget');
