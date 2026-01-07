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

class DayAndTimeAssignedTargetingOptionDetails extends \Google\Model
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
   * Time zone resolution is either unspecific or unknown.
   */
  public const TIME_ZONE_RESOLUTION_TIME_ZONE_RESOLUTION_UNSPECIFIED = 'TIME_ZONE_RESOLUTION_UNSPECIFIED';
  /**
   * Times are resolved in the time zone of the user that saw the ad.
   */
  public const TIME_ZONE_RESOLUTION_TIME_ZONE_RESOLUTION_END_USER = 'TIME_ZONE_RESOLUTION_END_USER';
  /**
   * Times are resolved in the time zone of the advertiser that served the ad.
   */
  public const TIME_ZONE_RESOLUTION_TIME_ZONE_RESOLUTION_ADVERTISER = 'TIME_ZONE_RESOLUTION_ADVERTISER';
  /**
   * Required. The day of the week for this day and time targeting setting.
   *
   * @var string
   */
  public $dayOfWeek;
  /**
   * Required. The end hour for day and time targeting. Must be between 1 (1
   * hour after start of day) and 24 (end of day).
   *
   * @var int
   */
  public $endHour;
  /**
   * Required. The start hour for day and time targeting. Must be between 0
   * (start of day) and 23 (1 hour before end of day).
   *
   * @var int
   */
  public $startHour;
  /**
   * Required. The mechanism used to determine which timezone to use for this
   * day and time targeting setting.
   *
   * @var string
   */
  public $timeZoneResolution;

  /**
   * Required. The day of the week for this day and time targeting setting.
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
   * Required. The end hour for day and time targeting. Must be between 1 (1
   * hour after start of day) and 24 (end of day).
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
   * Required. The start hour for day and time targeting. Must be between 0
   * (start of day) and 23 (1 hour before end of day).
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
   * Required. The mechanism used to determine which timezone to use for this
   * day and time targeting setting.
   *
   * Accepted values: TIME_ZONE_RESOLUTION_UNSPECIFIED,
   * TIME_ZONE_RESOLUTION_END_USER, TIME_ZONE_RESOLUTION_ADVERTISER
   *
   * @param self::TIME_ZONE_RESOLUTION_* $timeZoneResolution
   */
  public function setTimeZoneResolution($timeZoneResolution)
  {
    $this->timeZoneResolution = $timeZoneResolution;
  }
  /**
   * @return self::TIME_ZONE_RESOLUTION_*
   */
  public function getTimeZoneResolution()
  {
    return $this->timeZoneResolution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DayAndTimeAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_DayAndTimeAssignedTargetingOptionDetails');
