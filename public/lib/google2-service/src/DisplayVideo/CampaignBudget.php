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

class CampaignBudget extends \Google\Model
{
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
   * External budget source value is not specified or unknown in this version.
   */
  public const EXTERNAL_BUDGET_SOURCE_EXTERNAL_BUDGET_SOURCE_UNSPECIFIED = 'EXTERNAL_BUDGET_SOURCE_UNSPECIFIED';
  /**
   * Budget has no external source.
   */
  public const EXTERNAL_BUDGET_SOURCE_EXTERNAL_BUDGET_SOURCE_NONE = 'EXTERNAL_BUDGET_SOURCE_NONE';
  /**
   * Budget source is Mediaocean.
   */
  public const EXTERNAL_BUDGET_SOURCE_EXTERNAL_BUDGET_SOURCE_MEDIA_OCEAN = 'EXTERNAL_BUDGET_SOURCE_MEDIA_OCEAN';
  /**
   * Required. The total amount the linked insertion order segments can budget.
   * The amount is in micros. Must be greater than 0. For example, 500000000
   * represents 500 standard units of the currency.
   *
   * @var string
   */
  public $budgetAmountMicros;
  /**
   * The unique ID of the campaign budget. Assigned by the system. Do not set
   * for new budgets. Must be included when updating or adding budgets to
   * campaign_budgets. Otherwise, a new ID will be generated and assigned.
   *
   * @var string
   */
  public $budgetId;
  /**
   * Required. Immutable. Specifies whether the budget is measured in currency
   * or impressions.
   *
   * @var string
   */
  public $budgetUnit;
  protected $dateRangeType = DateRange::class;
  protected $dateRangeDataType = '';
  /**
   * Required. The display name of the budget. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Immutable. The ID identifying this budget to the external source. If this
   * field is set and the invoice detail level of the corresponding billing
   * profile is set to "Budget level PO", all impressions served against this
   * budget will include this ID on the invoice. Must be unique under the
   * campaign.
   *
   * @var string
   */
  public $externalBudgetId;
  /**
   * Required. The external source of the budget.
   *
   * @var string
   */
  public $externalBudgetSource;
  /**
   * Immutable. The ID used to group budgets to be included the same invoice. If
   * this field is set and the invoice level of the corresponding billing
   * profile is set to "Budget invoice grouping ID", all external_budget_id
   * sharing the same invoice_grouping_id will be grouped in the same invoice.
   *
   * @var string
   */
  public $invoiceGroupingId;
  protected $prismaConfigType = PrismaConfig::class;
  protected $prismaConfigDataType = '';

  /**
   * Required. The total amount the linked insertion order segments can budget.
   * The amount is in micros. Must be greater than 0. For example, 500000000
   * represents 500 standard units of the currency.
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
   * The unique ID of the campaign budget. Assigned by the system. Do not set
   * for new budgets. Must be included when updating or adding budgets to
   * campaign_budgets. Otherwise, a new ID will be generated and assigned.
   *
   * @param string $budgetId
   */
  public function setBudgetId($budgetId)
  {
    $this->budgetId = $budgetId;
  }
  /**
   * @return string
   */
  public function getBudgetId()
  {
    return $this->budgetId;
  }
  /**
   * Required. Immutable. Specifies whether the budget is measured in currency
   * or impressions.
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
   * Required. The date range for the campaign budget. Linked budget segments
   * may have a different date range. They are resolved relative to the parent
   * advertiser's time zone. Both `start_date` and `end_date` must be before the
   * year 2037.
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
   * Required. The display name of the budget. Must be UTF-8 encoded with a
   * maximum size of 240 bytes.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Immutable. The ID identifying this budget to the external source. If this
   * field is set and the invoice detail level of the corresponding billing
   * profile is set to "Budget level PO", all impressions served against this
   * budget will include this ID on the invoice. Must be unique under the
   * campaign.
   *
   * @param string $externalBudgetId
   */
  public function setExternalBudgetId($externalBudgetId)
  {
    $this->externalBudgetId = $externalBudgetId;
  }
  /**
   * @return string
   */
  public function getExternalBudgetId()
  {
    return $this->externalBudgetId;
  }
  /**
   * Required. The external source of the budget.
   *
   * Accepted values: EXTERNAL_BUDGET_SOURCE_UNSPECIFIED,
   * EXTERNAL_BUDGET_SOURCE_NONE, EXTERNAL_BUDGET_SOURCE_MEDIA_OCEAN
   *
   * @param self::EXTERNAL_BUDGET_SOURCE_* $externalBudgetSource
   */
  public function setExternalBudgetSource($externalBudgetSource)
  {
    $this->externalBudgetSource = $externalBudgetSource;
  }
  /**
   * @return self::EXTERNAL_BUDGET_SOURCE_*
   */
  public function getExternalBudgetSource()
  {
    return $this->externalBudgetSource;
  }
  /**
   * Immutable. The ID used to group budgets to be included the same invoice. If
   * this field is set and the invoice level of the corresponding billing
   * profile is set to "Budget invoice grouping ID", all external_budget_id
   * sharing the same invoice_grouping_id will be grouped in the same invoice.
   *
   * @param string $invoiceGroupingId
   */
  public function setInvoiceGroupingId($invoiceGroupingId)
  {
    $this->invoiceGroupingId = $invoiceGroupingId;
  }
  /**
   * @return string
   */
  public function getInvoiceGroupingId()
  {
    return $this->invoiceGroupingId;
  }
  /**
   * Additional metadata for use by the Mediaocean Prisma tool. Required for
   * Mediaocean budgets. Only applicable to prisma_enabled advertisers.
   *
   * @param PrismaConfig $prismaConfig
   */
  public function setPrismaConfig(PrismaConfig $prismaConfig)
  {
    $this->prismaConfig = $prismaConfig;
  }
  /**
   * @return PrismaConfig
   */
  public function getPrismaConfig()
  {
    return $this->prismaConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CampaignBudget::class, 'Google_Service_DisplayVideo_CampaignBudget');
