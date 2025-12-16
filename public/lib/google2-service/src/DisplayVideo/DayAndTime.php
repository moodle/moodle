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

class DayAndTime extends \Google\Model
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
   * Required. Day of the week.
   *
   * @var string
   */
  public $dayOfWeek;
  /**
   * Required. Hour of the day.
   *
   * @var int
   */
  public $hourOfDay;
  /**
   * Required. The mechanism used to determine the relevant timezone.
   *
   * @var string
   */
  public $timeZoneResolution;

  /**
   * Required. Day of the week.
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
   * Required. Hour of the day.
   *
   * @param int $hourOfDay
   */
  public function setHourOfDay($hourOfDay)
  {
    $this->hourOfDay = $hourOfDay;
  }
  /**
   * @return int
   */
  public function getHourOfDay()
  {
    return $this->hourOfDay;
  }
  /**
   * Required. The mechanism used to determine the relevant timezone.
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
class_alias(DayAndTime::class, 'Google_Service_DisplayVideo_DayAndTime');
