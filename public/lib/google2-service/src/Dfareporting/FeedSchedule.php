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

class FeedSchedule extends \Google\Model
{
  /**
   * Optional. The number of times the feed retransforms within one day. This is
   * a required field if the schedule is enabled. Acceptable values are between
   * 1 to 6, inclusive.
   *
   * @var string
   */
  public $repeatValue;
  /**
   * Optional. Whether the schedule is enabled.
   *
   * @var bool
   */
  public $scheduleEnabled;
  /**
   * Optional. The hour of the day to start the feed. It is applicable if the
   * repeat value is equal to 1. Default value is 0.
   *
   * @var string
   */
  public $startHour;
  /**
   * Optional. The minute of the hour to start the feed. It is applicable if the
   * repeat value is equal to 1. Default value is 0.
   *
   * @var string
   */
  public $startMinute;
  /**
   * Optional. The time zone to schedule the feed. It is applicable if the
   * repeat value is equal to 1. Default value is "America/Los_Angeles".
   *
   * @var string
   */
  public $timeZone;

  /**
   * Optional. The number of times the feed retransforms within one day. This is
   * a required field if the schedule is enabled. Acceptable values are between
   * 1 to 6, inclusive.
   *
   * @param string $repeatValue
   */
  public function setRepeatValue($repeatValue)
  {
    $this->repeatValue = $repeatValue;
  }
  /**
   * @return string
   */
  public function getRepeatValue()
  {
    return $this->repeatValue;
  }
  /**
   * Optional. Whether the schedule is enabled.
   *
   * @param bool $scheduleEnabled
   */
  public function setScheduleEnabled($scheduleEnabled)
  {
    $this->scheduleEnabled = $scheduleEnabled;
  }
  /**
   * @return bool
   */
  public function getScheduleEnabled()
  {
    return $this->scheduleEnabled;
  }
  /**
   * Optional. The hour of the day to start the feed. It is applicable if the
   * repeat value is equal to 1. Default value is 0.
   *
   * @param string $startHour
   */
  public function setStartHour($startHour)
  {
    $this->startHour = $startHour;
  }
  /**
   * @return string
   */
  public function getStartHour()
  {
    return $this->startHour;
  }
  /**
   * Optional. The minute of the hour to start the feed. It is applicable if the
   * repeat value is equal to 1. Default value is 0.
   *
   * @param string $startMinute
   */
  public function setStartMinute($startMinute)
  {
    $this->startMinute = $startMinute;
  }
  /**
   * @return string
   */
  public function getStartMinute()
  {
    return $this->startMinute;
  }
  /**
   * Optional. The time zone to schedule the feed. It is applicable if the
   * repeat value is equal to 1. Default value is "America/Los_Angeles".
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FeedSchedule::class, 'Google_Service_Dfareporting_FeedSchedule');
