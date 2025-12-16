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

namespace Google\Service\BackupforGKE;

class ExclusionWindow extends \Google\Model
{
  /**
   * The exclusion window occurs every day if set to "True". Specifying this
   * field to "False" is an error.
   *
   * @var bool
   */
  public $daily;
  protected $daysOfWeekType = DayOfWeekList::class;
  protected $daysOfWeekDataType = '';
  /**
   * Required. Specifies duration of the window. Duration must be >= 5 minutes
   * and < (target RPO - 20 minutes). Additional restrictions based on the
   * recurrence type to allow some time for backup to happen: -
   * single_occurrence_date: no restriction, but UI may warn about this when
   * duration >= target RPO - daily window: duration < 24 hours - weekly window:
   * - days of week includes all seven days of a week: duration < 24 hours - all
   * other weekly window: duration < 168 hours (i.e., 24 * 7 hours)
   *
   * @var string
   */
  public $duration;
  protected $singleOccurrenceDateType = Date::class;
  protected $singleOccurrenceDateDataType = '';
  protected $startTimeType = TimeOfDay::class;
  protected $startTimeDataType = '';

  /**
   * The exclusion window occurs every day if set to "True". Specifying this
   * field to "False" is an error.
   *
   * @param bool $daily
   */
  public function setDaily($daily)
  {
    $this->daily = $daily;
  }
  /**
   * @return bool
   */
  public function getDaily()
  {
    return $this->daily;
  }
  /**
   * The exclusion window occurs on these days of each week in UTC.
   *
   * @param DayOfWeekList $daysOfWeek
   */
  public function setDaysOfWeek(DayOfWeekList $daysOfWeek)
  {
    $this->daysOfWeek = $daysOfWeek;
  }
  /**
   * @return DayOfWeekList
   */
  public function getDaysOfWeek()
  {
    return $this->daysOfWeek;
  }
  /**
   * Required. Specifies duration of the window. Duration must be >= 5 minutes
   * and < (target RPO - 20 minutes). Additional restrictions based on the
   * recurrence type to allow some time for backup to happen: -
   * single_occurrence_date: no restriction, but UI may warn about this when
   * duration >= target RPO - daily window: duration < 24 hours - weekly window:
   * - days of week includes all seven days of a week: duration < 24 hours - all
   * other weekly window: duration < 168 hours (i.e., 24 * 7 hours)
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * No recurrence. The exclusion window occurs only once and on this date in
   * UTC.
   *
   * @param Date $singleOccurrenceDate
   */
  public function setSingleOccurrenceDate(Date $singleOccurrenceDate)
  {
    $this->singleOccurrenceDate = $singleOccurrenceDate;
  }
  /**
   * @return Date
   */
  public function getSingleOccurrenceDate()
  {
    return $this->singleOccurrenceDate;
  }
  /**
   * Optional. Specifies the start time of the window using time of the day in
   * UTC.
   *
   * @param TimeOfDay $startTime
   */
  public function setStartTime(TimeOfDay $startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return TimeOfDay
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExclusionWindow::class, 'Google_Service_BackupforGKE_ExclusionWindow');
