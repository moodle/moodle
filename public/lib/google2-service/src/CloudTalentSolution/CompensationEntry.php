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

namespace Google\Service\CloudTalentSolution;

class CompensationEntry extends \Google\Model
{
  /**
   * Default value.
   */
  public const TYPE_COMPENSATION_TYPE_UNSPECIFIED = 'COMPENSATION_TYPE_UNSPECIFIED';
  /**
   * Base compensation: Refers to the fixed amount of money paid to an employee
   * by an employer in return for work performed. Base compensation does not
   * include benefits, bonuses or any other potential compensation from an
   * employer.
   */
  public const TYPE_BASE = 'BASE';
  /**
   * Bonus.
   */
  public const TYPE_BONUS = 'BONUS';
  /**
   * Signing bonus.
   */
  public const TYPE_SIGNING_BONUS = 'SIGNING_BONUS';
  /**
   * Equity.
   */
  public const TYPE_EQUITY = 'EQUITY';
  /**
   * Profit sharing.
   */
  public const TYPE_PROFIT_SHARING = 'PROFIT_SHARING';
  /**
   * Commission.
   */
  public const TYPE_COMMISSIONS = 'COMMISSIONS';
  /**
   * Tips.
   */
  public const TYPE_TIPS = 'TIPS';
  /**
   * Other compensation type.
   */
  public const TYPE_OTHER_COMPENSATION_TYPE = 'OTHER_COMPENSATION_TYPE';
  /**
   * Default value.
   */
  public const UNIT_COMPENSATION_UNIT_UNSPECIFIED = 'COMPENSATION_UNIT_UNSPECIFIED';
  /**
   * Hourly.
   */
  public const UNIT_HOURLY = 'HOURLY';
  /**
   * Daily.
   */
  public const UNIT_DAILY = 'DAILY';
  /**
   * Weekly
   */
  public const UNIT_WEEKLY = 'WEEKLY';
  /**
   * Monthly.
   */
  public const UNIT_MONTHLY = 'MONTHLY';
  /**
   * Yearly.
   */
  public const UNIT_YEARLY = 'YEARLY';
  /**
   * One time.
   */
  public const UNIT_ONE_TIME = 'ONE_TIME';
  /**
   * Other compensation units.
   */
  public const UNIT_OTHER_COMPENSATION_UNIT = 'OTHER_COMPENSATION_UNIT';
  protected $amountType = Money::class;
  protected $amountDataType = '';
  /**
   * Compensation description. For example, could indicate equity terms or
   * provide additional context to an estimated bonus.
   *
   * @var string
   */
  public $description;
  /**
   * Expected number of units paid each year. If not specified, when
   * Job.employment_types is FULLTIME, a default value is inferred based on
   * unit. Default values: - HOURLY: 2080 - DAILY: 260 - WEEKLY: 52 - MONTHLY:
   * 12 - ANNUAL: 1
   *
   * @var 
   */
  public $expectedUnitsPerYear;
  protected $rangeType = CompensationRange::class;
  protected $rangeDataType = '';
  /**
   * Compensation type. Default is
   * CompensationType.COMPENSATION_TYPE_UNSPECIFIED.
   *
   * @var string
   */
  public $type;
  /**
   * Frequency of the specified amount. Default is
   * CompensationUnit.COMPENSATION_UNIT_UNSPECIFIED.
   *
   * @var string
   */
  public $unit;

  /**
   * Compensation amount.
   *
   * @param Money $amount
   */
  public function setAmount(Money $amount)
  {
    $this->amount = $amount;
  }
  /**
   * @return Money
   */
  public function getAmount()
  {
    return $this->amount;
  }
  /**
   * Compensation description. For example, could indicate equity terms or
   * provide additional context to an estimated bonus.
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
  public function setExpectedUnitsPerYear($expectedUnitsPerYear)
  {
    $this->expectedUnitsPerYear = $expectedUnitsPerYear;
  }
  public function getExpectedUnitsPerYear()
  {
    return $this->expectedUnitsPerYear;
  }
  /**
   * Compensation range.
   *
   * @param CompensationRange $range
   */
  public function setRange(CompensationRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return CompensationRange
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * Compensation type. Default is
   * CompensationType.COMPENSATION_TYPE_UNSPECIFIED.
   *
   * Accepted values: COMPENSATION_TYPE_UNSPECIFIED, BASE, BONUS, SIGNING_BONUS,
   * EQUITY, PROFIT_SHARING, COMMISSIONS, TIPS, OTHER_COMPENSATION_TYPE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Frequency of the specified amount. Default is
   * CompensationUnit.COMPENSATION_UNIT_UNSPECIFIED.
   *
   * Accepted values: COMPENSATION_UNIT_UNSPECIFIED, HOURLY, DAILY, WEEKLY,
   * MONTHLY, YEARLY, ONE_TIME, OTHER_COMPENSATION_UNIT
   *
   * @param self::UNIT_* $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return self::UNIT_*
   */
  public function getUnit()
  {
    return $this->unit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CompensationEntry::class, 'Google_Service_CloudTalentSolution_CompensationEntry');
