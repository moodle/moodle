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

namespace Google\Service\Backupdr;

class WeekDayOfMonth extends \Google\Model
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
   * The zero value. Do not use.
   */
  public const WEEK_OF_MONTH_WEEK_OF_MONTH_UNSPECIFIED = 'WEEK_OF_MONTH_UNSPECIFIED';
  /**
   * The first week of the month.
   */
  public const WEEK_OF_MONTH_FIRST = 'FIRST';
  /**
   * The second week of the month.
   */
  public const WEEK_OF_MONTH_SECOND = 'SECOND';
  /**
   * The third week of the month.
   */
  public const WEEK_OF_MONTH_THIRD = 'THIRD';
  /**
   * The fourth week of the month.
   */
  public const WEEK_OF_MONTH_FOURTH = 'FOURTH';
  /**
   * The last week of the month.
   */
  public const WEEK_OF_MONTH_LAST = 'LAST';
  /**
   * Required. Specifies the day of the week.
   *
   * @var string
   */
  public $dayOfWeek;
  /**
   * Required. Specifies the week of the month.
   *
   * @var string
   */
  public $weekOfMonth;

  /**
   * Required. Specifies the day of the week.
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
   * Required. Specifies the week of the month.
   *
   * Accepted values: WEEK_OF_MONTH_UNSPECIFIED, FIRST, SECOND, THIRD, FOURTH,
   * LAST
   *
   * @param self::WEEK_OF_MONTH_* $weekOfMonth
   */
  public function setWeekOfMonth($weekOfMonth)
  {
    $this->weekOfMonth = $weekOfMonth;
  }
  /**
   * @return self::WEEK_OF_MONTH_*
   */
  public function getWeekOfMonth()
  {
    return $this->weekOfMonth;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WeekDayOfMonth::class, 'Google_Service_Backupdr_WeekDayOfMonth');
