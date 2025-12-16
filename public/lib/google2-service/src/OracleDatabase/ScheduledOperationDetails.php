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

namespace Google\Service\OracleDatabase;

class ScheduledOperationDetails extends \Google\Model
{
  /**
   * The day of the week is unspecified.
   */
  public const DAY_OF_WEEK_DAY_OF_WEEK_UNSPECIFIED = 'DAY_OF_WEEK_UNSPECIFIED';
  /**
   * Monday
   */
  public const DAY_OF_WEEK_MONDAY = 'MONDAY';
  /**
   * Tuesday
   */
  public const DAY_OF_WEEK_TUESDAY = 'TUESDAY';
  /**
   * Wednesday
   */
  public const DAY_OF_WEEK_WEDNESDAY = 'WEDNESDAY';
  /**
   * Thursday
   */
  public const DAY_OF_WEEK_THURSDAY = 'THURSDAY';
  /**
   * Friday
   */
  public const DAY_OF_WEEK_FRIDAY = 'FRIDAY';
  /**
   * Saturday
   */
  public const DAY_OF_WEEK_SATURDAY = 'SATURDAY';
  /**
   * Sunday
   */
  public const DAY_OF_WEEK_SUNDAY = 'SUNDAY';
  /**
   * Output only. Day of week.
   *
   * @var string
   */
  public $dayOfWeek;
  protected $startTimeType = TimeOfDay::class;
  protected $startTimeDataType = '';
  protected $stopTimeType = TimeOfDay::class;
  protected $stopTimeDataType = '';

  /**
   * Output only. Day of week.
   *
   * Accepted values: DAY_OF_WEEK_UNSPECIFIED, MONDAY, TUESDAY, WEDNESDAY,
   * THURSDAY, FRIDAY, SATURDAY, SUNDAY
   *
   * @param self::DAY_OF_WEEK_* $dayOfWeek
   */
  public function setDayOfWeek($dayOfWeek)
  {
    $this->dayOfWeek = $dayOfWeek;
  }
  /**
   * @return self::DAY_OF_WEEK_*
   */
  public function getDayOfWeek()
  {
    return $this->dayOfWeek;
  }
  /**
   * Output only. Auto start time.
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
  /**
   * Output only. Auto stop time.
   *
   * @param TimeOfDay $stopTime
   */
  public function setStopTime(TimeOfDay $stopTime)
  {
    $this->stopTime = $stopTime;
  }
  /**
   * @return TimeOfDay
   */
  public function getStopTime()
  {
    return $this->stopTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScheduledOperationDetails::class, 'Google_Service_OracleDatabase_ScheduledOperationDetails');
