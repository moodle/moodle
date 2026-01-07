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

class FinancialDetails extends \Google\Model
{
  protected $costOfElectricityWithoutSolarType = Money::class;
  protected $costOfElectricityWithoutSolarDataType = '';
  protected $federalIncentiveType = Money::class;
  protected $federalIncentiveDataType = '';
  /**
   * How many AC kWh we think the solar panels will generate in their first
   * year.
   *
   * @var float
   */
  public $initialAcKwhPerYear;
  protected $lifetimeSrecTotalType = Money::class;
  protected $lifetimeSrecTotalDataType = '';
  /**
   * Whether net metering is allowed.
   *
   * @var bool
   */
  public $netMeteringAllowed;
  /**
   * The percentage (0-100) of solar electricity production we assumed was
   * exported to the grid, based on the first quarter of production. This
   * affects the calculations if net metering is not allowed.
   *
   * @var float
   */
  public $percentageExportedToGrid;
  protected $remainingLifetimeUtilityBillType = Money::class;
  protected $remainingLifetimeUtilityBillDataType = '';
  /**
   * Percentage (0-100) of the user's power supplied by solar. Valid for the
   * first year but approximately correct for future years.
   *
   * @var float
   */
  public $solarPercentage;
  protected $stateIncentiveType = Money::class;
  protected $stateIncentiveDataType = '';
  protected $utilityIncentiveType = Money::class;
  protected $utilityIncentiveDataType = '';

  /**
   * Total cost of electricity the user would have paid over the lifetime period
   * if they didn't install solar.
   *
   * @param Money $costOfElectricityWithoutSolar
   */
  public function setCostOfElectricityWithoutSolar(Money $costOfElectricityWithoutSolar)
  {
    $this->costOfElectricityWithoutSolar = $costOfElectricityWithoutSolar;
  }
  /**
   * @return Money
   */
  public function getCostOfElectricityWithoutSolar()
  {
    return $this->costOfElectricityWithoutSolar;
  }
  /**
   * Amount of money available from federal incentives; this applies if the user
   * buys (with or without a loan) the panels.
   *
   * @param Money $federalIncentive
   */
  public function setFederalIncentive(Money $federalIncentive)
  {
    $this->federalIncentive = $federalIncentive;
  }
  /**
   * @return Money
   */
  public function getFederalIncentive()
  {
    return $this->federalIncentive;
  }
  /**
   * How many AC kWh we think the solar panels will generate in their first
   * year.
   *
   * @param float $initialAcKwhPerYear
   */
  public function setInitialAcKwhPerYear($initialAcKwhPerYear)
  {
    $this->initialAcKwhPerYear = $initialAcKwhPerYear;
  }
  /**
   * @return float
   */
  public function getInitialAcKwhPerYear()
  {
    return $this->initialAcKwhPerYear;
  }
  /**
   * Amount of money the user will receive from Solar Renewable Energy Credits
   * over the panel lifetime; this applies if the user buys (with or without a
   * loan) the panels.
   *
   * @param Money $lifetimeSrecTotal
   */
  public function setLifetimeSrecTotal(Money $lifetimeSrecTotal)
  {
    $this->lifetimeSrecTotal = $lifetimeSrecTotal;
  }
  /**
   * @return Money
   */
  public function getLifetimeSrecTotal()
  {
    return $this->lifetimeSrecTotal;
  }
  /**
   * Whether net metering is allowed.
   *
   * @param bool $netMeteringAllowed
   */
  public function setNetMeteringAllowed($netMeteringAllowed)
  {
    $this->netMeteringAllowed = $netMeteringAllowed;
  }
  /**
   * @return bool
   */
  public function getNetMeteringAllowed()
  {
    return $this->netMeteringAllowed;
  }
  /**
   * The percentage (0-100) of solar electricity production we assumed was
   * exported to the grid, based on the first quarter of production. This
   * affects the calculations if net metering is not allowed.
   *
   * @param float $percentageExportedToGrid
   */
  public function setPercentageExportedToGrid($percentageExportedToGrid)
  {
    $this->percentageExportedToGrid = $percentageExportedToGrid;
  }
  /**
   * @return float
   */
  public function getPercentageExportedToGrid()
  {
    return $this->percentageExportedToGrid;
  }
  /**
   * Utility bill for electricity not produced by solar, for the lifetime of the
   * panels.
   *
   * @param Money $remainingLifetimeUtilityBill
   */
  public function setRemainingLifetimeUtilityBill(Money $remainingLifetimeUtilityBill)
  {
    $this->remainingLifetimeUtilityBill = $remainingLifetimeUtilityBill;
  }
  /**
   * @return Money
   */
  public function getRemainingLifetimeUtilityBill()
  {
    return $this->remainingLifetimeUtilityBill;
  }
  /**
   * Percentage (0-100) of the user's power supplied by solar. Valid for the
   * first year but approximately correct for future years.
   *
   * @param float $solarPercentage
   */
  public function setSolarPercentage($solarPercentage)
  {
    $this->solarPercentage = $solarPercentage;
  }
  /**
   * @return float
   */
  public function getSolarPercentage()
  {
    return $this->solarPercentage;
  }
  /**
   * Amount of money available from state incentives; this applies if the user
   * buys (with or without a loan) the panels.
   *
   * @param Money $stateIncentive
   */
  public function setStateIncentive(Money $stateIncentive)
  {
    $this->stateIncentive = $stateIncentive;
  }
  /**
   * @return Money
   */
  public function getStateIncentive()
  {
    return $this->stateIncentive;
  }
  /**
   * Amount of money available from utility incentives; this applies if the user
   * buys (with or without a loan) the panels.
   *
   * @param Money $utilityIncentive
   */
  public function setUtilityIncentive(Money $utilityIncentive)
  {
    $this->utilityIncentive = $utilityIncentive;
  }
  /**
   * @return Money
   */
  public function getUtilityIncentive()
  {
    return $this->utilityIncentive;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FinancialDetails::class, 'Google_Service_Solar_FinancialDetails');
