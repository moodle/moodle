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

class GoogleCloudBillingBudgetsV1ThresholdRule extends \Google\Model
{
  /**
   * Unspecified threshold basis.
   */
  public const SPEND_BASIS_BASIS_UNSPECIFIED = 'BASIS_UNSPECIFIED';
  /**
   * Use current spend as the basis for comparison against the threshold.
   */
  public const SPEND_BASIS_CURRENT_SPEND = 'CURRENT_SPEND';
  /**
   * Use forecasted spend for the period as the basis for comparison against the
   * threshold. FORECASTED_SPEND can only be set when the budget's time period
   * is a Filter.calendar_period. It cannot be set in combination with
   * Filter.custom_period.
   */
  public const SPEND_BASIS_FORECASTED_SPEND = 'FORECASTED_SPEND';
  /**
   * Optional. The type of basis used to determine if spend has passed the
   * threshold. Behavior defaults to CURRENT_SPEND if not set.
   *
   * @var string
   */
  public $spendBasis;
  /**
   * Required. Send an alert when this threshold is exceeded. This is a
   * 1.0-based percentage, so 0.5 = 50%. Validation: non-negative number.
   *
   * @var 
   */
  public $thresholdPercent;

  /**
   * Optional. The type of basis used to determine if spend has passed the
   * threshold. Behavior defaults to CURRENT_SPEND if not set.
   *
   * Accepted values: BASIS_UNSPECIFIED, CURRENT_SPEND, FORECASTED_SPEND
   *
   * @param self::SPEND_BASIS_* $spendBasis
   */
  public function setSpendBasis($spendBasis)
  {
    $this->spendBasis = $spendBasis;
  }
  /**
   * @return self::SPEND_BASIS_*
   */
  public function getSpendBasis()
  {
    return $this->spendBasis;
  }
  public function setThresholdPercent($thresholdPercent)
  {
    $this->thresholdPercent = $thresholdPercent;
  }
  public function getThresholdPercent()
  {
    return $this->thresholdPercent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBillingBudgetsV1ThresholdRule::class, 'Google_Service_CloudBillingBudget_GoogleCloudBillingBudgetsV1ThresholdRule');
