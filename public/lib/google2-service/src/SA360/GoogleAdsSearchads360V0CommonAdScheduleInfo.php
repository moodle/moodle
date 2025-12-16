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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonAdScheduleInfo extends \Google\Model
{
  /**
   * Not specified.
   */
  public const DAY_OF_WEEK_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The value is unknown in this version.
   */
  public const DAY_OF_WEEK_UNKNOWN = 'UNKNOWN';
  /**
   * Monday.
   */
  public const DAY_OF_WEEK_MONDAY = 'MONDAY';
  /**
   * Tuesday.
   */
  public const DAY_OF_WEEK_TUESDAY = 'TUESDAY';
  /**
   * Wednesday.
   */
  public const DAY_OF_WEEK_WEDNESDAY = 'WEDNESDAY';
  /**
   * Thursday.
   */
  public const DAY_OF_WEEK_THURSDAY = 'THURSDAY';
  /**
   * Friday.
   */
  public const DAY_OF_WEEK_FRIDAY = 'FRIDAY';
  /**
   * Saturday.
   */
  public const DAY_OF_WEEK_SATURDAY = 'SATURDAY';
  /**
   * Sunday.
   */
  public const DAY_OF_WEEK_SUNDAY = 'SUNDAY';
  /**
   * Not specified.
   */
  public const END_MINUTE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The value is unknown in this version.
   */
  public const END_MINUTE_UNKNOWN = 'UNKNOWN';
  /**
   * Zero minutes past the hour.
   */
  public const END_MINUTE_ZERO = 'ZERO';
  /**
   * Fifteen minutes past the hour.
   */
  public const END_MINUTE_FIFTEEN = 'FIFTEEN';
  /**
   * Thirty minutes past the hour.
   */
  public const END_MINUTE_THIRTY = 'THIRTY';
  /**
   * Forty-five minutes past the hour.
   */
  public const END_MINUTE_FORTY_FIVE = 'FORTY_FIVE';
  /**
   * Not specified.
   */
  public const START_MINUTE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The value is unknown in this version.
   */
  public const START_MINUTE_UNKNOWN = 'UNKNOWN';
  /**
   * Zero minutes past the hour.
   */
  public const START_MINUTE_ZERO = 'ZERO';
  /**
   * Fifteen minutes past the hour.
   */
  public const START_MINUTE_FIFTEEN = 'FIFTEEN';
  /**
   * Thirty minutes past the hour.
   */
  public const START_MINUTE_THIRTY = 'THIRTY';
  /**
   * Forty-five minutes past the hour.
   */
  public const START_MINUTE_FORTY_FIVE = 'FORTY_FIVE';
  /**
   * Day of the week the schedule applies to. This field is required for CREATE
   * operations and is prohibited on UPDATE operations.
   *
   * @var string
   */
  public $dayOfWeek;
  /**
   * Ending hour in 24 hour time; 24 signifies end of the day. This field must
   * be between 0 and 24, inclusive. This field is required for CREATE
   * operations and is prohibited on UPDATE operations.
   *
   * @var int
   */
  public $endHour;
  /**
   * Minutes after the end hour at which this schedule ends. The schedule is
   * exclusive of the end minute. This field is required for CREATE operations
   * and is prohibited on UPDATE operations.
   *
   * @var string
   */
  public $endMinute;
  /**
   * Starting hour in 24 hour time. This field must be between 0 and 23,
   * inclusive. This field is required for CREATE operations and is prohibited
   * on UPDATE operations.
   *
   * @var int
   */
  public $startHour;
  /**
   * Minutes after the start hour at which this schedule starts. This field is
   * required for CREATE operations and is prohibited on UPDATE operations.
   *
   * @var string
   */
  public $startMinute;

  /**
   * Day of the week the schedule applies to. This field is required for CREATE
   * operations and is prohibited on UPDATE operations.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, MONDAY, TUESDAY, WEDNESDAY,
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
   * Ending hour in 24 hour time; 24 signifies end of the day. This field must
   * be between 0 and 24, inclusive. This field is required for CREATE
   * operations and is prohibited on UPDATE operations.
   *
   * @param int $endHour
   */
  public function setEndHour($endHour)
  {
    $this->endHour = $endHour;
  }
  /**
   * @return int
   */
  public function getEndHour()
  {
    return $this->endHour;
  }
  /**
   * Minutes after the end hour at which this schedule ends. The schedule is
   * exclusive of the end minute. This field is required for CREATE operations
   * and is prohibited on UPDATE operations.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ZERO, FIFTEEN, THIRTY, FORTY_FIVE
   *
   * @param self::END_MINUTE_* $endMinute
   */
  public function setEndMinute($endMinute)
  {
    $this->endMinute = $endMinute;
  }
  /**
   * @return self::END_MINUTE_*
   */
  public function getEndMinute()
  {
    return $this->endMinute;
  }
  /**
   * Starting hour in 24 hour time. This field must be between 0 and 23,
   * inclusive. This field is required for CREATE operations and is prohibited
   * on UPDATE operations.
   *
   * @param int $startHour
   */
  public function setStartHour($startHour)
  {
    $this->startHour = $startHour;
  }
  /**
   * @return int
   */
  public function getStartHour()
  {
    return $this->startHour;
  }
  /**
   * Minutes after the start hour at which this schedule starts. This field is
   * required for CREATE operations and is prohibited on UPDATE operations.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ZERO, FIFTEEN, THIRTY, FORTY_FIVE
   *
   * @param self::START_MINUTE_* $startMinute
   */
  public function setStartMinute($startMinute)
  {
    $this->startMinute = $startMinute;
  }
  /**
   * @return self::START_MINUTE_*
   */
  public function getStartMinute()
  {
    return $this->startMinute;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonAdScheduleInfo::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonAdScheduleInfo');
