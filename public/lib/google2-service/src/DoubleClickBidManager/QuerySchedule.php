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

namespace Google\Service\DoubleClickBidManager;

class QuerySchedule extends \Google\Model
{
  /**
   * Default value when frequency is not specified or is unknown in this
   * version.
   */
  public const FREQUENCY_FREQUENCY_UNSPECIFIED = 'FREQUENCY_UNSPECIFIED';
  /**
   * Only when the query is run manually.
   */
  public const FREQUENCY_ONE_TIME = 'ONE_TIME';
  /**
   * Once a day.
   */
  public const FREQUENCY_DAILY = 'DAILY';
  /**
   * Once a week.
   */
  public const FREQUENCY_WEEKLY = 'WEEKLY';
  /**
   * Twice a month.
   */
  public const FREQUENCY_SEMI_MONTHLY = 'SEMI_MONTHLY';
  /**
   * Once a month.
   */
  public const FREQUENCY_MONTHLY = 'MONTHLY';
  /**
   * Once a quarter.
   */
  public const FREQUENCY_QUARTERLY = 'QUARTERLY';
  /**
   * Once a year.
   */
  public const FREQUENCY_YEARLY = 'YEARLY';
  protected $endDateType = Date::class;
  protected $endDateDataType = '';
  /**
   * How frequently to run the query. If set to `ONE_TIME`, the query will only
   * be run when queries.run is called.
   *
   * @var string
   */
  public $frequency;
  /**
   * The canonical code for the timezone the query schedule is based on.
   * Scheduled runs are usually conducted in the morning of a given day.
   * Defaults to `America/New_York`.
   *
   * @var string
   */
  public $nextRunTimezoneCode;
  protected $startDateType = Date::class;
  protected $startDateDataType = '';

  /**
   * The date on which to end the scheduled runs. This field is required if
   * frequency is not set to `ONE_TIME`. Otherwise, it will be ignored.
   *
   * @param Date $endDate
   */
  public function setEndDate(Date $endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return Date
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * How frequently to run the query. If set to `ONE_TIME`, the query will only
   * be run when queries.run is called.
   *
   * Accepted values: FREQUENCY_UNSPECIFIED, ONE_TIME, DAILY, WEEKLY,
   * SEMI_MONTHLY, MONTHLY, QUARTERLY, YEARLY
   *
   * @param self::FREQUENCY_* $frequency
   */
  public function setFrequency($frequency)
  {
    $this->frequency = $frequency;
  }
  /**
   * @return self::FREQUENCY_*
   */
  public function getFrequency()
  {
    return $this->frequency;
  }
  /**
   * The canonical code for the timezone the query schedule is based on.
   * Scheduled runs are usually conducted in the morning of a given day.
   * Defaults to `America/New_York`.
   *
   * @param string $nextRunTimezoneCode
   */
  public function setNextRunTimezoneCode($nextRunTimezoneCode)
  {
    $this->nextRunTimezoneCode = $nextRunTimezoneCode;
  }
  /**
   * @return string
   */
  public function getNextRunTimezoneCode()
  {
    return $this->nextRunTimezoneCode;
  }
  /**
   * The date on which to begin the scheduled runs. This field is required if
   * frequency is not set to `ONE_TIME`. Otherwise, it will be ignored.
   *
   * @param Date $startDate
   */
  public function setStartDate(Date $startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return Date
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QuerySchedule::class, 'Google_Service_DoubleClickBidManager_QuerySchedule');
