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

class FinancialAnalysis extends \Google\Model
{
  /**
   * How much electricity the house uses in an average month, based on the bill
   * size and the local electricity rates.
   *
   * @var float
   */
  public $averageKwhPerMonth;
  protected $cashPurchaseSavingsType = CashPurchaseSavings::class;
  protected $cashPurchaseSavingsDataType = '';
  /**
   * Whether this is the bill size selected to be the default bill for the area
   * this building is in. Exactly one `FinancialAnalysis` in
   * `BuildingSolarPotential` should have `default_bill` set.
   *
   * @var bool
   */
  public $defaultBill;
  protected $financedPurchaseSavingsType = FinancedPurchaseSavings::class;
  protected $financedPurchaseSavingsDataType = '';
  protected $financialDetailsType = FinancialDetails::class;
  protected $financialDetailsDataType = '';
  protected $leasingSavingsType = LeasingSavings::class;
  protected $leasingSavingsDataType = '';
  protected $monthlyBillType = Money::class;
  protected $monthlyBillDataType = '';
  /**
   * Index in solar_panel_configs of the optimum solar layout for this bill
   * size. This can be -1 indicating that there is no layout. In this case, the
   * remaining submessages will be omitted.
   *
   * @var int
   */
  public $panelConfigIndex;

  /**
   * How much electricity the house uses in an average month, based on the bill
   * size and the local electricity rates.
   *
   * @param float $averageKwhPerMonth
   */
  public function setAverageKwhPerMonth($averageKwhPerMonth)
  {
    $this->averageKwhPerMonth = $averageKwhPerMonth;
  }
  /**
   * @return float
   */
  public function getAverageKwhPerMonth()
  {
    return $this->averageKwhPerMonth;
  }
  /**
   * Cost and benefit of buying the solar panels with cash.
   *
   * @param CashPurchaseSavings $cashPurchaseSavings
   */
  public function setCashPurchaseSavings(CashPurchaseSavings $cashPurchaseSavings)
  {
    $this->cashPurchaseSavings = $cashPurchaseSavings;
  }
  /**
   * @return CashPurchaseSavings
   */
  public function getCashPurchaseSavings()
  {
    return $this->cashPurchaseSavings;
  }
  /**
   * Whether this is the bill size selected to be the default bill for the area
   * this building is in. Exactly one `FinancialAnalysis` in
   * `BuildingSolarPotential` should have `default_bill` set.
   *
   * @param bool $defaultBill
   */
  public function setDefaultBill($defaultBill)
  {
    $this->defaultBill = $defaultBill;
  }
  /**
   * @return bool
   */
  public function getDefaultBill()
  {
    return $this->defaultBill;
  }
  /**
   * Cost and benefit of buying the solar panels by financing the purchase.
   *
   * @param FinancedPurchaseSavings $financedPurchaseSavings
   */
  public function setFinancedPurchaseSavings(FinancedPurchaseSavings $financedPurchaseSavings)
  {
    $this->financedPurchaseSavings = $financedPurchaseSavings;
  }
  /**
   * @return FinancedPurchaseSavings
   */
  public function getFinancedPurchaseSavings()
  {
    return $this->financedPurchaseSavings;
  }
  /**
   * Financial information that applies regardless of the financing method used.
   *
   * @param FinancialDetails $financialDetails
   */
  public function setFinancialDetails(FinancialDetails $financialDetails)
  {
    $this->financialDetails = $financialDetails;
  }
  /**
   * @return FinancialDetails
   */
  public function getFinancialDetails()
  {
    return $this->financialDetails;
  }
  /**
   * Cost and benefit of leasing the solar panels.
   *
   * @param LeasingSavings $leasingSavings
   */
  public function setLeasingSavings(LeasingSavings $leasingSavings)
  {
    $this->leasingSavings = $leasingSavings;
  }
  /**
   * @return LeasingSavings
   */
  public function getLeasingSavings()
  {
    return $this->leasingSavings;
  }
  /**
   * The monthly electric bill this analysis assumes.
   *
   * @param Money $monthlyBill
   */
  public function setMonthlyBill(Money $monthlyBill)
  {
    $this->monthlyBill = $monthlyBill;
  }
  /**
   * @return Money
   */
  public function getMonthlyBill()
  {
    return $this->monthlyBill;
  }
  /**
   * Index in solar_panel_configs of the optimum solar layout for this bill
   * size. This can be -1 indicating that there is no layout. In this case, the
   * remaining submessages will be omitted.
   *
   * @param int $panelConfigIndex
   */
  public function setPanelConfigIndex($panelConfigIndex)
  {
    $this->panelConfigIndex = $panelConfigIndex;
  }
  /**
   * @return int
   */
  public function getPanelConfigIndex()
  {
    return $this->panelConfigIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FinancialAnalysis::class, 'Google_Service_Solar_FinancialAnalysis');
