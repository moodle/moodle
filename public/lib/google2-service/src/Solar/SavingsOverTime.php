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

class SavingsOverTime extends \Google\Model
{
  /**
   * Indicates whether this scenario is financially viable. Will be false for
   * scenarios with poor financial viability (e.g., money-losing).
   *
   * @var bool
   */
  public $financiallyViable;
  protected $presentValueOfSavingsLifetimeType = Money::class;
  protected $presentValueOfSavingsLifetimeDataType = '';
  protected $presentValueOfSavingsYear20Type = Money::class;
  protected $presentValueOfSavingsYear20DataType = '';
  protected $savingsLifetimeType = Money::class;
  protected $savingsLifetimeDataType = '';
  protected $savingsYear1Type = Money::class;
  protected $savingsYear1DataType = '';
  protected $savingsYear20Type = Money::class;
  protected $savingsYear20DataType = '';

  /**
   * Indicates whether this scenario is financially viable. Will be false for
   * scenarios with poor financial viability (e.g., money-losing).
   *
   * @param bool $financiallyViable
   */
  public function setFinanciallyViable($financiallyViable)
  {
    $this->financiallyViable = $financiallyViable;
  }
  /**
   * @return bool
   */
  public function getFinanciallyViable()
  {
    return $this->financiallyViable;
  }
  /**
   * Using the assumed discount rate, what is the present value of the
   * cumulative lifetime savings?
   *
   * @param Money $presentValueOfSavingsLifetime
   */
  public function setPresentValueOfSavingsLifetime(Money $presentValueOfSavingsLifetime)
  {
    $this->presentValueOfSavingsLifetime = $presentValueOfSavingsLifetime;
  }
  /**
   * @return Money
   */
  public function getPresentValueOfSavingsLifetime()
  {
    return $this->presentValueOfSavingsLifetime;
  }
  /**
   * Using the assumed discount rate, what is the present value of the
   * cumulative 20-year savings?
   *
   * @param Money $presentValueOfSavingsYear20
   */
  public function setPresentValueOfSavingsYear20(Money $presentValueOfSavingsYear20)
  {
    $this->presentValueOfSavingsYear20 = $presentValueOfSavingsYear20;
  }
  /**
   * @return Money
   */
  public function getPresentValueOfSavingsYear20()
  {
    return $this->presentValueOfSavingsYear20;
  }
  /**
   * Savings in the entire panel lifetime.
   *
   * @param Money $savingsLifetime
   */
  public function setSavingsLifetime(Money $savingsLifetime)
  {
    $this->savingsLifetime = $savingsLifetime;
  }
  /**
   * @return Money
   */
  public function getSavingsLifetime()
  {
    return $this->savingsLifetime;
  }
  /**
   * Savings in the first year after panel installation.
   *
   * @param Money $savingsYear1
   */
  public function setSavingsYear1(Money $savingsYear1)
  {
    $this->savingsYear1 = $savingsYear1;
  }
  /**
   * @return Money
   */
  public function getSavingsYear1()
  {
    return $this->savingsYear1;
  }
  /**
   * Savings in the first twenty years after panel installation.
   *
   * @param Money $savingsYear20
   */
  public function setSavingsYear20(Money $savingsYear20)
  {
    $this->savingsYear20 = $savingsYear20;
  }
  /**
   * @return Money
   */
  public function getSavingsYear20()
  {
    return $this->savingsYear20;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SavingsOverTime::class, 'Google_Service_Solar_SavingsOverTime');
