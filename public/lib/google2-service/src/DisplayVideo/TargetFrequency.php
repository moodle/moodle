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

class TargetFrequency extends \Google\Model
{
  /**
   * Time unit value is not specified or is unknown in this version.
   */
  public const TIME_UNIT_TIME_UNIT_UNSPECIFIED = 'TIME_UNIT_UNSPECIFIED';
  /**
   * The frequency cap will be applied to the whole life time of the line item.
   *
   * @deprecated
   */
  public const TIME_UNIT_TIME_UNIT_LIFETIME = 'TIME_UNIT_LIFETIME';
  /**
   * The frequency cap will be applied to a number of months.
   */
  public const TIME_UNIT_TIME_UNIT_MONTHS = 'TIME_UNIT_MONTHS';
  /**
   * The frequency cap will be applied to a number of weeks.
   */
  public const TIME_UNIT_TIME_UNIT_WEEKS = 'TIME_UNIT_WEEKS';
  /**
   * The frequency cap will be applied to a number of days.
   */
  public const TIME_UNIT_TIME_UNIT_DAYS = 'TIME_UNIT_DAYS';
  /**
   * The frequency cap will be applied to a number of hours.
   */
  public const TIME_UNIT_TIME_UNIT_HOURS = 'TIME_UNIT_HOURS';
  /**
   * The frequency cap will be applied to a number of minutes.
   */
  public const TIME_UNIT_TIME_UNIT_MINUTES = 'TIME_UNIT_MINUTES';
  /**
   * The target number of times, on average, the ads will be shown to the same
   * person in the timespan dictated by time_unit and time_unit_count.
   *
   * @var string
   */
  public $targetCount;
  /**
   * The unit of time in which the target frequency will be applied. The
   * following time unit is applicable: * `TIME_UNIT_WEEKS`
   *
   * @var string
   */
  public $timeUnit;
  /**
   * The number of time_unit the target frequency will last. The following
   * restrictions apply based on the value of time_unit: * `TIME_UNIT_WEEKS` -
   * must be 1
   *
   * @var int
   */
  public $timeUnitCount;

  /**
   * The target number of times, on average, the ads will be shown to the same
   * person in the timespan dictated by time_unit and time_unit_count.
   *
   * @param string $targetCount
   */
  public function setTargetCount($targetCount)
  {
    $this->targetCount = $targetCount;
  }
  /**
   * @return string
   */
  public function getTargetCount()
  {
    return $this->targetCount;
  }
  /**
   * The unit of time in which the target frequency will be applied. The
   * following time unit is applicable: * `TIME_UNIT_WEEKS`
   *
   * Accepted values: TIME_UNIT_UNSPECIFIED, TIME_UNIT_LIFETIME,
   * TIME_UNIT_MONTHS, TIME_UNIT_WEEKS, TIME_UNIT_DAYS, TIME_UNIT_HOURS,
   * TIME_UNIT_MINUTES
   *
   * @param self::TIME_UNIT_* $timeUnit
   */
  public function setTimeUnit($timeUnit)
  {
    $this->timeUnit = $timeUnit;
  }
  /**
   * @return self::TIME_UNIT_*
   */
  public function getTimeUnit()
  {
    return $this->timeUnit;
  }
  /**
   * The number of time_unit the target frequency will last. The following
   * restrictions apply based on the value of time_unit: * `TIME_UNIT_WEEKS` -
   * must be 1
   *
   * @param int $timeUnitCount
   */
  public function setTimeUnitCount($timeUnitCount)
  {
    $this->timeUnitCount = $timeUnitCount;
  }
  /**
   * @return int
   */
  public function getTimeUnitCount()
  {
    return $this->timeUnitCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetFrequency::class, 'Google_Service_DisplayVideo_TargetFrequency');
