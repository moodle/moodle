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

namespace Google\Service\DisplayVideo;

class LineItemBudget extends \Google\Model
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const BUDGET_ALLOCATION_TYPE_LINE_ITEM_BUDGET_ALLOCATION_TYPE_UNSPECIFIED = 'LINE_ITEM_BUDGET_ALLOCATION_TYPE_UNSPECIFIED';
  /**
   * Automatic budget allocation is enabled for the line item.
   */
  public const BUDGET_ALLOCATION_TYPE_LINE_ITEM_BUDGET_ALLOCATION_TYPE_AUTOMATIC = 'LINE_ITEM_BUDGET_ALLOCATION_TYPE_AUTOMATIC';
  /**
   * A fixed max budget amount is allocated for the line item.
   */
  public const BUDGET_ALLOCATION_TYPE_LINE_ITEM_BUDGET_ALLOCATION_TYPE_FIXED = 'LINE_ITEM_BUDGET_ALLOCATION_TYPE_FIXED';
  /**
   * No budget limit is applied to the line item.
   */
  public const BUDGET_ALLOCATION_TYPE_LINE_ITEM_BUDGET_ALLOCATION_TYPE_UNLIMITED = 'LINE_ITEM_BUDGET_ALLOCATION_TYPE_UNLIMITED';
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const BUDGET_UNIT_BUDGET_UNIT_UNSPECIFIED = 'BUDGET_UNIT_UNSPECIFIED';
  /**
   * Budgeting in currency amounts.
   */
  public const BUDGET_UNIT_BUDGET_UNIT_CURRENCY = 'BUDGET_UNIT_CURRENCY';
  /**
   * Budgeting in impression amounts.
   */
  public const BUDGET_UNIT_BUDGET_UNIT_IMPRESSIONS = 'BUDGET_UNIT_IMPRESSIONS';
  /**
   * Required. The type of the budget allocation.
   * `LINE_ITEM_BUDGET_ALLOCATION_TYPE_AUTOMATIC` is only applicable when
   * automatic budget allocation is enabled for the parent insertion order.
   *
   * @var string
   */
  public $budgetAllocationType;
  /**
   * Output only. The budget unit specifies whether the budget is currency based
   * or impression based. This value is inherited from the parent insertion
   * order.
   *
   * @var string
   */
  public $budgetUnit;
  /**
   * The maximum budget amount the line item will spend. Must be greater than 0.
   * When budget_allocation_type is: *
   * `LINE_ITEM_BUDGET_ALLOCATION_TYPE_AUTOMATIC`, this field is immutable and
   * is set by the system. * `LINE_ITEM_BUDGET_ALLOCATION_TYPE_FIXED`, if
   * budget_unit is: - `BUDGET_UNIT_CURRENCY`, this field represents maximum
   * budget amount to spend, in micros of the advertiser's currency. For
   * example, 1500000 represents 1.5 standard units of the currency. -
   * `BUDGET_UNIT_IMPRESSIONS`, this field represents the maximum number of
   * impressions to serve. * `LINE_ITEM_BUDGET_ALLOCATION_TYPE_UNLIMITED`, this
   * field is not applicable and will be ignored by the system.
   *
   * @var string
   */
  public $maxAmount;

  /**
   * Required. The type of the budget allocation.
   * `LINE_ITEM_BUDGET_ALLOCATION_TYPE_AUTOMATIC` is only applicable when
   * automatic budget allocation is enabled for the parent insertion order.
   *
   * Accepted values: LINE_ITEM_BUDGET_ALLOCATION_TYPE_UNSPECIFIED,
   * LINE_ITEM_BUDGET_ALLOCATION_TYPE_AUTOMATIC,
   * LINE_ITEM_BUDGET_ALLOCATION_TYPE_FIXED,
   * LINE_ITEM_BUDGET_ALLOCATION_TYPE_UNLIMITED
   *
   * @param self::BUDGET_ALLOCATION_TYPE_* $budgetAllocationType
   */
  public function setBudgetAllocationType($budgetAllocationType)
  {
    $this->budgetAllocationType = $budgetAllocationType;
  }
  /**
   * @return self::BUDGET_ALLOCATION_TYPE_*
   */
  public function getBudgetAllocationType()
  {
    return $this->budgetAllocationType;
  }
  /**
   * Output only. The budget unit specifies whether the budget is currency based
   * or impression based. This value is inherited from the parent insertion
   * order.
   *
   * Accepted values: BUDGET_UNIT_UNSPECIFIED, BUDGET_UNIT_CURRENCY,
   * BUDGET_UNIT_IMPRESSIONS
   *
   * @param self::BUDGET_UNIT_* $budgetUnit
   */
  public function setBudgetUnit($budgetUnit)
  {
    $this->budgetUnit = $budgetUnit;
  }
  /**
   * @return self::BUDGET_UNIT_*
   */
  public function getBudgetUnit()
  {
    return $this->budgetUnit;
  }
  /**
   * The maximum budget amount the line item will spend. Must be greater than 0.
   * When budget_allocation_type is: *
   * `LINE_ITEM_BUDGET_ALLOCATION_TYPE_AUTOMATIC`, this field is immutable and
   * is set by the system. * `LINE_ITEM_BUDGET_ALLOCATION_TYPE_FIXED`, if
   * budget_unit is: - `BUDGET_UNIT_CURRENCY`, this field represents maximum
   * budget amount to spend, in micros of the advertiser's currency. For
   * example, 1500000 represents 1.5 standard units of the currency. -
   * `BUDGET_UNIT_IMPRESSIONS`, this field represents the maximum number of
   * impressions to serve. * `LINE_ITEM_BUDGET_ALLOCATION_TYPE_UNLIMITED`, this
   * field is not applicable and will be ignored by the system.
   *
   * @param string $maxAmount
   */
  public function setMaxAmount($maxAmount)
  {
    $this->maxAmount = $maxAmount;
  }
  /**
   * @return string
   */
  public function getMaxAmount()
  {
    return $this->maxAmount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LineItemBudget::class, 'Google_Service_DisplayVideo_LineItemBudget');
