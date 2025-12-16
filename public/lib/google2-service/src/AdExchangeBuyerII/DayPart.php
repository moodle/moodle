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

namespace Google\Service\AdExchangeBuyerII;

class DayPart extends \Google\Model
{
  /**
   * A placeholder for when the day of the week is not specified.
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
   * The day of the week to target. If unspecified, applicable to all days.
   *
   * @var string
   */
  public $dayOfWeek;
  protected $endTimeType = TimeOfDay::class;
  protected $endTimeDataType = '';
  protected $startTimeType = TimeOfDay::class;
  protected $startTimeDataType = '';

  /**
   * The day of the week to target. If unspecified, applicable to all days.
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
   * The ending time of the day for the ad to show (minute level granularity).
   * The end time is exclusive. This field is not available for filtering in PQL
   * queries.
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
   * The starting time of day for the ad to show (minute level granularity). The
   * start time is inclusive. This field is not available for filtering in PQL
   * queries.
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
class_alias(DayPart::class, 'Google_Service_AdExchangeBuyerII_DayPart');
