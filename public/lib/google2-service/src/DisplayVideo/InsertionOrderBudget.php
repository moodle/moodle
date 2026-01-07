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

class InsertionOrderBudget extends \Google\Collection
{
  /**
   * Insertion order automation option is not specified or is unknown in this
   * version.
   */
  public const AUTOMATION_TYPE_INSERTION_ORDER_AUTOMATION_TYPE_UNSPECIFIED = 'INSERTION_ORDER_AUTOMATION_TYPE_UNSPECIFIED';
  /**
   * Automatic budget allocation. Allow the system to automatically shift budget
   * to owning line items to optimize performance defined by kpi. No automation
   * on bid settings.
   */
  public const AUTOMATION_TYPE_INSERTION_ORDER_AUTOMATION_TYPE_BUDGET = 'INSERTION_ORDER_AUTOMATION_TYPE_BUDGET';
  /**
   * No automation of bid or budget on insertion order level. Bid and budget
   * must be manually configured at the line item level.
   */
  public const AUTOMATION_TYPE_INSERTION_ORDER_AUTOMATION_TYPE_NONE = 'INSERTION_ORDER_AUTOMATION_TYPE_NONE';
  /**
   * Allow the system to automatically adjust bids and shift budget to owning
   * line items to optimize performance defined by kpi.
   */
  public const AUTOMATION_TYPE_INSERTION_ORDER_AUTOMATION_TYPE_BID_BUDGET = 'INSERTION_ORDER_AUTOMATION_TYPE_BID_BUDGET';
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
  protected $collection_key = 'budgetSegments';
  /**
   * Optional. The type of automation used to manage bid and budget for the
   * insertion order. If this field is unspecified in creation, the value
   * defaults to `INSERTION_ORDER_AUTOMATION_TYPE_NONE`.
   *
   * @var string
   */
  public $automationType;
  protected $budgetSegmentsType = InsertionOrderBudgetSegment::class;
  protected $budgetSegmentsDataType = 'array';
  /**
   * Required. Immutable. The budget unit specifies whether the budget is
   * currency based or impression based.
   *
   * @var string
   */
  public $budgetUnit;

  /**
   * Optional. The type of automation used to manage bid and budget for the
   * insertion order. If this field is unspecified in creation, the value
   * defaults to `INSERTION_ORDER_AUTOMATION_TYPE_NONE`.
   *
   * Accepted values: INSERTION_ORDER_AUTOMATION_TYPE_UNSPECIFIED,
   * INSERTION_ORDER_AUTOMATION_TYPE_BUDGET,
   * INSERTION_ORDER_AUTOMATION_TYPE_NONE,
   * INSERTION_ORDER_AUTOMATION_TYPE_BID_BUDGET
   *
   * @param self::AUTOMATION_TYPE_* $automationType
   */
  public function setAutomationType($automationType)
  {
    $this->automationType = $automationType;
  }
  /**
   * @return self::AUTOMATION_TYPE_*
   */
  public function getAutomationType()
  {
    return $this->automationType;
  }
  /**
   * Required. The list of budget segments. Use a budget segment to specify a
   * specific budget for a given period of time an insertion order is running.
   *
   * @param InsertionOrderBudgetSegment[] $budgetSegments
   */
  public function setBudgetSegments($budgetSegments)
  {
    $this->budgetSegments = $budgetSegments;
  }
  /**
   * @return InsertionOrderBudgetSegment[]
   */
  public function getBudgetSegments()
  {
    return $this->budgetSegments;
  }
  /**
   * Required. Immutable. The budget unit specifies whether the budget is
   * currency based or impression based.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsertionOrderBudget::class, 'Google_Service_DisplayVideo_InsertionOrderBudget');
