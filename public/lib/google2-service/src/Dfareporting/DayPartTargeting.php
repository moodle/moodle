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

class DayPartTargeting extends \Google\Collection
{
  protected $collection_key = 'hoursOfDay';
  /**
   * Days of the week when the ad will serve. Acceptable values are: - "SUNDAY"
   * - "MONDAY" - "TUESDAY" - "WEDNESDAY" - "THURSDAY" - "FRIDAY" - "SATURDAY"
   *
   * @var string[]
   */
  public $daysOfWeek;
  /**
   * Hours of the day when the ad will serve, where 0 is midnight to 1 AM and 23
   * is 11 PM to midnight. Can be specified with days of week, in which case the
   * ad would serve during these hours on the specified days. For example if
   * Monday, Wednesday, Friday are the days of week specified and 9-10am, 3-5pm
   * (hours 9, 15, and 16) is specified, the ad would serve Monday, Wednesdays,
   * and Fridays at 9-10am and 3-5pm. Acceptable values are 0 to 23, inclusive.
   *
   * @var int[]
   */
  public $hoursOfDay;
  /**
   * Whether or not to use the user's local time. If false, the America/New York
   * time zone applies.
   *
   * @var bool
   */
  public $userLocalTime;

  /**
   * Days of the week when the ad will serve. Acceptable values are: - "SUNDAY"
   * - "MONDAY" - "TUESDAY" - "WEDNESDAY" - "THURSDAY" - "FRIDAY" - "SATURDAY"
   *
   * @param string[] $daysOfWeek
   */
  public function setDaysOfWeek($daysOfWeek)
  {
    $this->daysOfWeek = $daysOfWeek;
  }
  /**
   * @return string[]
   */
  public function getDaysOfWeek()
  {
    return $this->daysOfWeek;
  }
  /**
   * Hours of the day when the ad will serve, where 0 is midnight to 1 AM and 23
   * is 11 PM to midnight. Can be specified with days of week, in which case the
   * ad would serve during these hours on the specified days. For example if
   * Monday, Wednesday, Friday are the days of week specified and 9-10am, 3-5pm
   * (hours 9, 15, and 16) is specified, the ad would serve Monday, Wednesdays,
   * and Fridays at 9-10am and 3-5pm. Acceptable values are 0 to 23, inclusive.
   *
   * @param int[] $hoursOfDay
   */
  public function setHoursOfDay($hoursOfDay)
  {
    $this->hoursOfDay = $hoursOfDay;
  }
  /**
   * @return int[]
   */
  public function getHoursOfDay()
  {
    return $this->hoursOfDay;
  }
  /**
   * Whether or not to use the user's local time. If false, the America/New York
   * time zone applies.
   *
   * @param bool $userLocalTime
   */
  public function setUserLocalTime($userLocalTime)
  {
    $this->userLocalTime = $userLocalTime;
  }
  /**
   * @return bool
   */
  public function getUserLocalTime()
  {
    return $this->userLocalTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DayPartTargeting::class, 'Google_Service_Dfareporting_DayPartTargeting');
