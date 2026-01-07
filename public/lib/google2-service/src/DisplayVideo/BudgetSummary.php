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

class BudgetSummary extends \Google\Model
{
  /**
   * Corresponds to the external_budget_id of a campaign budget. If the value is
   * not set in the campaign budget, this field will be empty.
   *
   * @var string
   */
  public $externalBudgetId;
  /**
   * The sum of charges made under this budget before taxes, in micros of the
   * invoice's currency. For example, if currency_code is `USD`, then 1000000
   * represents one US dollar.
   *
   * @var string
   */
  public $preTaxAmountMicros;
  protected $prismaCpeCodeType = PrismaCpeCode::class;
  protected $prismaCpeCodeDataType = '';
  /**
   * The amount of tax applied to charges under this budget, in micros of the
   * invoice's currency. For example, if currency_code is `USD`, then 1000000
   * represents one US dollar.
   *
   * @var string
   */
  public $taxAmountMicros;
  /**
   * The total sum of charges made under this budget, including tax, in micros
   * of the invoice's currency. For example, if currency_code is `USD`, then
   * 1000000 represents one US dollar.
   *
   * @var string
   */
  public $totalAmountMicros;

  /**
   * Corresponds to the external_budget_id of a campaign budget. If the value is
   * not set in the campaign budget, this field will be empty.
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
   * The sum of charges made under this budget before taxes, in micros of the
   * invoice's currency. For example, if currency_code is `USD`, then 1000000
   * represents one US dollar.
   *
   * @param string $preTaxAmountMicros
   */
  public function setPreTaxAmountMicros($preTaxAmountMicros)
  {
    $this->preTaxAmountMicros = $preTaxAmountMicros;
  }
  /**
   * @return string
   */
  public function getPreTaxAmountMicros()
  {
    return $this->preTaxAmountMicros;
  }
  /**
   * Relevant client, product, and estimate codes from the Mediaocean Prisma
   * tool. Only applicable for campaign budgets with an external_budget_source
   * of EXTERNAL_BUDGET_SOURCE_MEDIA_OCEAN.
   *
   * @param PrismaCpeCode $prismaCpeCode
   */
  public function setPrismaCpeCode(PrismaCpeCode $prismaCpeCode)
  {
    $this->prismaCpeCode = $prismaCpeCode;
  }
  /**
   * @return PrismaCpeCode
   */
  public function getPrismaCpeCode()
  {
    return $this->prismaCpeCode;
  }
  /**
   * The amount of tax applied to charges under this budget, in micros of the
   * invoice's currency. For example, if currency_code is `USD`, then 1000000
   * represents one US dollar.
   *
   * @param string $taxAmountMicros
   */
  public function setTaxAmountMicros($taxAmountMicros)
  {
    $this->taxAmountMicros = $taxAmountMicros;
  }
  /**
   * @return string
   */
  public function getTaxAmountMicros()
  {
    return $this->taxAmountMicros;
  }
  /**
   * The total sum of charges made under this budget, including tax, in micros
   * of the invoice's currency. For example, if currency_code is `USD`, then
   * 1000000 represents one US dollar.
   *
   * @param string $totalAmountMicros
   */
  public function setTotalAmountMicros($totalAmountMicros)
  {
    $this->totalAmountMicros = $totalAmountMicros;
  }
  /**
   * @return string
   */
  public function getTotalAmountMicros()
  {
    return $this->totalAmountMicros;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BudgetSummary::class, 'Google_Service_DisplayVideo_BudgetSummary');
