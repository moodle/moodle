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

class InsertionOrderBudgetSegment extends \Google\Model
{
  /**
   * Required. The budget amount the insertion order will spend for the given
   * date_range. The amount is in micros. Must be greater than 0. For example,
   * 500000000 represents 500 standard units of the currency.
   *
   * @var string
   */
  public $budgetAmountMicros;
  /**
   * Optional. The budget_id of the campaign budget that this insertion order
   * budget segment is a part of.
   *
   * @var string
   */
  public $campaignBudgetId;
  protected $dateRangeType = DateRange::class;
  protected $dateRangeDataType = '';
  /**
   * Optional. The budget segment description. It can be used to enter Purchase
   * Order information for each budget segment and have that information printed
   * on the invoices. Must be UTF-8 encoded.
   *
   * @var string
   */
  public $description;

  /**
   * Required. The budget amount the insertion order will spend for the given
   * date_range. The amount is in micros. Must be greater than 0. For example,
   * 500000000 represents 500 standard units of the currency.
   *
   * @param string $budgetAmountMicros
   */
  public function setBudgetAmountMicros($budgetAmountMicros)
  {
    $this->budgetAmountMicros = $budgetAmountMicros;
  }
  /**
   * @return string
   */
  public function getBudgetAmountMicros()
  {
    return $this->budgetAmountMicros;
  }
  /**
   * Optional. The budget_id of the campaign budget that this insertion order
   * budget segment is a part of.
   *
   * @param string $campaignBudgetId
   */
  public function setCampaignBudgetId($campaignBudgetId)
  {
    $this->campaignBudgetId = $campaignBudgetId;
  }
  /**
   * @return string
   */
  public function getCampaignBudgetId()
  {
    return $this->campaignBudgetId;
  }
  /**
   * Required. The start and end date settings of the budget segment. They are
   * resolved relative to the parent advertiser's time zone. * When creating a
   * new budget segment, both `start_date` and `end_date` must be in the future.
   * * An existing budget segment with a `start_date` in the past has a mutable
   * `end_date` but an immutable `start_date`. * `end_date` must be the
   * `start_date` or later, both before the year 2037.
   *
   * @param DateRange $dateRange
   */
  public function setDateRange(DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }
  /**
   * @return DateRange
   */
  public function getDateRange()
  {
    return $this->dateRange;
  }
  /**
   * Optional. The budget segment description. It can be used to enter Purchase
   * Order information for each budget segment and have that information printed
   * on the invoices. Must be UTF-8 encoded.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsertionOrderBudgetSegment::class, 'Google_Service_DisplayVideo_InsertionOrderBudgetSegment');
