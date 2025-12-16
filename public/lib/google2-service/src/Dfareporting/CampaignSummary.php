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

namespace Google\Service\Dfareporting;

class CampaignSummary extends \Google\Model
{
  /**
   * Campaign billing invoice code.
   *
   * @var string
   */
  public $billingInvoiceCode;
  /**
   * Campaign ID.
   *
   * @var string
   */
  public $campaignId;
  /**
   * The pre-tax amount for this campaign, in micros of the invoice's currency.
   *
   * @var string
   */
  public $preTaxAmountMicros;
  /**
   * The tax amount for this campaign, in micros of the invoice's currency.
   *
   * @var string
   */
  public $taxAmountMicros;
  /**
   * The total amount of charges for this campaign, in micros of the invoice's
   * currency.
   *
   * @var string
   */
  public $totalAmountMicros;

  /**
   * Campaign billing invoice code.
   *
   * @param string $billingInvoiceCode
   */
  public function setBillingInvoiceCode($billingInvoiceCode)
  {
    $this->billingInvoiceCode = $billingInvoiceCode;
  }
  /**
   * @return string
   */
  public function getBillingInvoiceCode()
  {
    return $this->billingInvoiceCode;
  }
  /**
   * Campaign ID.
   *
   * @param string $campaignId
   */
  public function setCampaignId($campaignId)
  {
    $this->campaignId = $campaignId;
  }
  /**
   * @return string
   */
  public function getCampaignId()
  {
    return $this->campaignId;
  }
  /**
   * The pre-tax amount for this campaign, in micros of the invoice's currency.
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
   * The tax amount for this campaign, in micros of the invoice's currency.
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
   * The total amount of charges for this campaign, in micros of the invoice's
   * currency.
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
class_alias(CampaignSummary::class, 'Google_Service_Dfareporting_CampaignSummary');
