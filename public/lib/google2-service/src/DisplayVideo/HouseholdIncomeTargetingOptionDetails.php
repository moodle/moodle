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

class HouseholdIncomeTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when household income is not specified in this version. This
   * enum is a placeholder for default value and does not represent a real
   * household income option.
   */
  public const HOUSEHOLD_INCOME_HOUSEHOLD_INCOME_UNSPECIFIED = 'HOUSEHOLD_INCOME_UNSPECIFIED';
  /**
   * The household income of the audience is unknown.
   */
  public const HOUSEHOLD_INCOME_HOUSEHOLD_INCOME_UNKNOWN = 'HOUSEHOLD_INCOME_UNKNOWN';
  /**
   * The audience is in the lower 50% of U.S. household incomes.
   */
  public const HOUSEHOLD_INCOME_HOUSEHOLD_INCOME_LOWER_50_PERCENT = 'HOUSEHOLD_INCOME_LOWER_50_PERCENT';
  /**
   * The audience is in the top 41-50% of U.S. household incomes.
   */
  public const HOUSEHOLD_INCOME_HOUSEHOLD_INCOME_TOP_41_TO_50_PERCENT = 'HOUSEHOLD_INCOME_TOP_41_TO_50_PERCENT';
  /**
   * The audience is in the top 31-40% of U.S. household incomes.
   */
  public const HOUSEHOLD_INCOME_HOUSEHOLD_INCOME_TOP_31_TO_40_PERCENT = 'HOUSEHOLD_INCOME_TOP_31_TO_40_PERCENT';
  /**
   * The audience is in the top 21-30% of U.S. household incomes.
   */
  public const HOUSEHOLD_INCOME_HOUSEHOLD_INCOME_TOP_21_TO_30_PERCENT = 'HOUSEHOLD_INCOME_TOP_21_TO_30_PERCENT';
  /**
   * The audience is in the top 11-20% of U.S. household incomes.
   */
  public const HOUSEHOLD_INCOME_HOUSEHOLD_INCOME_TOP_11_TO_20_PERCENT = 'HOUSEHOLD_INCOME_TOP_11_TO_20_PERCENT';
  /**
   * The audience is in the top 10% of U.S. household incomes.
   */
  public const HOUSEHOLD_INCOME_HOUSEHOLD_INCOME_TOP_10_PERCENT = 'HOUSEHOLD_INCOME_TOP_10_PERCENT';
  /**
   * Output only. The household income of an audience.
   *
   * @var string
   */
  public $householdIncome;

  /**
   * Output only. The household income of an audience.
   *
   * Accepted values: HOUSEHOLD_INCOME_UNSPECIFIED, HOUSEHOLD_INCOME_UNKNOWN,
   * HOUSEHOLD_INCOME_LOWER_50_PERCENT, HOUSEHOLD_INCOME_TOP_41_TO_50_PERCENT,
   * HOUSEHOLD_INCOME_TOP_31_TO_40_PERCENT,
   * HOUSEHOLD_INCOME_TOP_21_TO_30_PERCENT,
   * HOUSEHOLD_INCOME_TOP_11_TO_20_PERCENT, HOUSEHOLD_INCOME_TOP_10_PERCENT
   *
   * @param self::HOUSEHOLD_INCOME_* $householdIncome
   */
  public function setHouseholdIncome($householdIncome)
  {
    $this->householdIncome = $householdIncome;
  }
  /**
   * @return self::HOUSEHOLD_INCOME_*
   */
  public function getHouseholdIncome()
  {
    return $this->householdIncome;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HouseholdIncomeTargetingOptionDetails::class, 'Google_Service_DisplayVideo_HouseholdIncomeTargetingOptionDetails');
