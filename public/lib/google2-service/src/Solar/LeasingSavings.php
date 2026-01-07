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

class LeasingSavings extends \Google\Model
{
  protected $annualLeasingCostType = Money::class;
  protected $annualLeasingCostDataType = '';
  /**
   * Whether leases are allowed in this juristiction (leases are not allowed in
   * some states). If this field is false, then the values in this message
   * should probably be ignored.
   *
   * @var bool
   */
  public $leasesAllowed;
  /**
   * Whether leases are supported in this juristiction by the financial
   * calculation engine. If this field is false, then the values in this message
   * should probably be ignored. This is independent of `leases_allowed`: in
   * some areas leases are allowed, but under conditions that aren't handled by
   * the financial models.
   *
   * @var bool
   */
  public $leasesSupported;
  protected $savingsType = SavingsOverTime::class;
  protected $savingsDataType = '';

  /**
   * Estimated annual leasing cost.
   *
   * @param Money $annualLeasingCost
   */
  public function setAnnualLeasingCost(Money $annualLeasingCost)
  {
    $this->annualLeasingCost = $annualLeasingCost;
  }
  /**
   * @return Money
   */
  public function getAnnualLeasingCost()
  {
    return $this->annualLeasingCost;
  }
  /**
   * Whether leases are allowed in this juristiction (leases are not allowed in
   * some states). If this field is false, then the values in this message
   * should probably be ignored.
   *
   * @param bool $leasesAllowed
   */
  public function setLeasesAllowed($leasesAllowed)
  {
    $this->leasesAllowed = $leasesAllowed;
  }
  /**
   * @return bool
   */
  public function getLeasesAllowed()
  {
    return $this->leasesAllowed;
  }
  /**
   * Whether leases are supported in this juristiction by the financial
   * calculation engine. If this field is false, then the values in this message
   * should probably be ignored. This is independent of `leases_allowed`: in
   * some areas leases are allowed, but under conditions that aren't handled by
   * the financial models.
   *
   * @param bool $leasesSupported
   */
  public function setLeasesSupported($leasesSupported)
  {
    $this->leasesSupported = $leasesSupported;
  }
  /**
   * @return bool
   */
  public function getLeasesSupported()
  {
    return $this->leasesSupported;
  }
  /**
   * How much is saved (or not) over the lifetime period.
   *
   * @param SavingsOverTime $savings
   */
  public function setSavings(SavingsOverTime $savings)
  {
    $this->savings = $savings;
  }
  /**
   * @return SavingsOverTime
   */
  public function getSavings()
  {
    return $this->savings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LeasingSavings::class, 'Google_Service_Solar_LeasingSavings');
