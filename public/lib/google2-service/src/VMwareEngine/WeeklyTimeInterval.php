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

namespace Google\Service\VMwareEngine;

class WeeklyTimeInterval extends \Google\Model
{
  /**
   * The day of the week is unspecified.
   */
  public const END_DAY_DAY_OF_WEEK_UNSPECIFIED = 'DAY_OF_WEEK_UNSPECIFIED';
  /**
   * Monday
   */
  public const END_DAY_MONDAY = 'MONDAY';
  /**
   * Tuesday
   */
  public const END_DAY_TUESDAY = 'TUESDAY';
  /**
   * Wednesday
   */
  public const END_DAY_WEDNESDAY = 'WEDNESDAY';
  /**
   * Thursday
   */
  public const END_DAY_THURSDAY = 'THURSDAY';
  /**
   * Friday
   */
  public const END_DAY_FRIDAY = 'FRIDAY';
  /**
   * Saturday
   */
  public const END_DAY_SATURDAY = 'SATURDAY';
  /**
   * Sunday
   */
  public const END_DAY_SUNDAY = 'SUNDAY';
  /**
   * The day of the week is unspecified.
   */
  public const START_DAY_DAY_OF_WEEK_UNSPECIFIED = 'DAY_OF_WEEK_UNSPECIFIED';
  /**
   * Monday
   */
  public const START_DAY_MONDAY = 'MONDAY';
  /**
   * Tuesday
   */
  public const START_DAY_TUESDAY = 'TUESDAY';
  /**
   * Wednesday
   */
  public const START_DAY_WEDNESDAY = 'WEDNESDAY';
  /**
   * Thursday
   */
  public const START_DAY_THURSDAY = 'THURSDAY';
  /**
   * Friday
   */
  public const START_DAY_FRIDAY = 'FRIDAY';
  /**
   * Saturday
   */
  public const START_DAY_SATURDAY = 'SATURDAY';
  /**
   * Sunday
   */
  public const START_DAY_SUNDAY = 'SUNDAY';
  /**
   * Output only. The day on which the interval ends. Can be same as start day.
   *
   * @var string
   */
  public $endDay;
  protected $endTimeType = TimeOfDay::class;
  protected $endTimeDataType = '';
  /**
   * Output only. The day on which the interval starts.
   *
   * @var string
   */
  public $startDay;
  protected $startTimeType = TimeOfDay::class;
  protected $startTimeDataType = '';

  /**
   * Output only. The day on which the interval ends. Can be same as start day.
   *
   * Accepted values: DAY_OF_WEEK_UNSPECIFIED, MONDAY, TUESDAY, WEDNESDAY,
   * THURSDAY, FRIDAY, SATURDAY, SUNDAY
   *
   * @param self::END_DAY_* $endDay
   */
  public function setEndDay($endDay)
  {
    $this->endDay = $endDay;
  }
  /**
   * @return self::END_DAY_*
   */
  public function getEndDay()
  {
    return $this->endDay;
  }
  /**
   * Output only. The time on the end day at which the interval ends.
   *
   * @param TimeOfDay $endTime
   */
  public function setEndTime(TimeOfDay $endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return TimeOfDay
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. The day on which the interval starts.
   *
   * Accepted values: DAY_OF_WEEK_UNSPECIFIED, MONDAY, TUESDAY, WEDNESDAY,
   * THURSDAY, FRIDAY, SATURDAY, SUNDAY
   *
   * @param self::START_DAY_* $startDay
   */
  public function setStartDay($startDay)
  {
    $this->startDay = $startDay;
  }
  /**
   * @return self::START_DAY_*
   */
  public function getStartDay()
  {
    return $this->startDay;
  }
  /**
   * Output only. The time on the start day at which the interval starts.
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
class_alias(WeeklyTimeInterval::class, 'Google_Service_VMwareEngine_WeeklyTimeInterval');
