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

class ReportSchedule extends \Google\Collection
{
  public const RUNS_ON_DAY_OF_MONTH_DAY_OF_MONTH = 'DAY_OF_MONTH';
  public const RUNS_ON_DAY_OF_MONTH_WEEK_OF_MONTH = 'WEEK_OF_MONTH';
  protected $collection_key = 'repeatsOnWeekDays';
  /**
   * Whether the schedule is active or not. Must be set to either true or false.
   *
   * @var bool
   */
  public $active;
  /**
   * Defines every how many days, weeks or months the report should be run.
   * Needs to be set when "repeats" is either "DAILY", "WEEKLY" or "MONTHLY".
   *
   * @var int
   */
  public $every;
  /**
   * @var string
   */
  public $expirationDate;
  /**
   * The interval for which the report is repeated. Note: - "DAILY" also
   * requires field "every" to be set. - "WEEKLY" also requires fields "every"
   * and "repeatsOnWeekDays" to be set. - "MONTHLY" also requires fields "every"
   * and "runsOnDayOfMonth" to be set.
   *
   * @var string
   */
  public $repeats;
  /**
   * List of week days "WEEKLY" on which scheduled reports should run.
   *
   * @var string[]
   */
  public $repeatsOnWeekDays;
  /**
   * Enum to define for "MONTHLY" scheduled reports whether reports should be
   * repeated on the same day of the month as "startDate" or the same day of the
   * week of the month. Example: If 'startDate' is Monday, April 2nd 2012
   * (2012-04-02), "DAY_OF_MONTH" would run subsequent reports on the 2nd of
   * every Month, and "WEEK_OF_MONTH" would run subsequent reports on the first
   * Monday of the month.
   *
   * @var string
   */
  public $runsOnDayOfMonth;
  /**
   * @var string
   */
  public $startDate;
  /**
   * The timezone when the report will run.
   *
   * @var string
   */
  public $timezone;

  /**
   * Whether the schedule is active or not. Must be set to either true or false.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Defines every how many days, weeks or months the report should be run.
   * Needs to be set when "repeats" is either "DAILY", "WEEKLY" or "MONTHLY".
   *
   * @param int $every
   */
  public function setEvery($every)
  {
    $this->every = $every;
  }
  /**
   * @return int
   */
  public function getEvery()
  {
    return $this->every;
  }
  /**
   * @param string $expirationDate
   */
  public function setExpirationDate($expirationDate)
  {
    $this->expirationDate = $expirationDate;
  }
  /**
   * @return string
   */
  public function getExpirationDate()
  {
    return $this->expirationDate;
  }
  /**
   * The interval for which the report is repeated. Note: - "DAILY" also
   * requires field "every" to be set. - "WEEKLY" also requires fields "every"
   * and "repeatsOnWeekDays" to be set. - "MONTHLY" also requires fields "every"
   * and "runsOnDayOfMonth" to be set.
   *
   * @param string $repeats
   */
  public function setRepeats($repeats)
  {
    $this->repeats = $repeats;
  }
  /**
   * @return string
   */
  public function getRepeats()
  {
    return $this->repeats;
  }
  /**
   * List of week days "WEEKLY" on which scheduled reports should run.
   *
   * @param string[] $repeatsOnWeekDays
   */
  public function setRepeatsOnWeekDays($repeatsOnWeekDays)
  {
    $this->repeatsOnWeekDays = $repeatsOnWeekDays;
  }
  /**
   * @return string[]
   */
  public function getRepeatsOnWeekDays()
  {
    return $this->repeatsOnWeekDays;
  }
  /**
   * Enum to define for "MONTHLY" scheduled reports whether reports should be
   * repeated on the same day of the month as "startDate" or the same day of the
   * week of the month. Example: If 'startDate' is Monday, April 2nd 2012
   * (2012-04-02), "DAY_OF_MONTH" would run subsequent reports on the 2nd of
   * every Month, and "WEEK_OF_MONTH" would run subsequent reports on the first
   * Monday of the month.
   *
   * Accepted values: DAY_OF_MONTH, WEEK_OF_MONTH
   *
   * @param self::RUNS_ON_DAY_OF_MONTH_* $runsOnDayOfMonth
   */
  public function setRunsOnDayOfMonth($runsOnDayOfMonth)
  {
    $this->runsOnDayOfMonth = $runsOnDayOfMonth;
  }
  /**
   * @return self::RUNS_ON_DAY_OF_MONTH_*
   */
  public function getRunsOnDayOfMonth()
  {
    return $this->runsOnDayOfMonth;
  }
  /**
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * The timezone when the report will run.
   *
   * @param string $timezone
   */
  public function setTimezone($timezone)
  {
    $this->timezone = $timezone;
  }
  /**
   * @return string
   */
  public function getTimezone()
  {
    return $this->timezone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportSchedule::class, 'Google_Service_Dfareporting_ReportSchedule');
