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

namespace Google\Service\Compute;

class ResourcePolicyWeeklyCycleDayOfWeek extends \Google\Model
{
  public const DAY_FRIDAY = 'FRIDAY';
  public const DAY_INVALID = 'INVALID';
  public const DAY_MONDAY = 'MONDAY';
  public const DAY_SATURDAY = 'SATURDAY';
  public const DAY_SUNDAY = 'SUNDAY';
  public const DAY_THURSDAY = 'THURSDAY';
  public const DAY_TUESDAY = 'TUESDAY';
  public const DAY_WEDNESDAY = 'WEDNESDAY';
  /**
   * Defines a schedule that runs on specific days of the week. Specify one or
   * more days. The following options are available: MONDAY, TUESDAY, WEDNESDAY,
   * THURSDAY, FRIDAY, SATURDAY, SUNDAY.
   *
   * @var string
   */
  public $day;
  /**
   * Output only. [Output only] Duration of the time window, automatically
   * chosen to be smallest possible in the given scenario.
   *
   * @var string
   */
  public $duration;
  /**
   * Time within the window to start the operations. It must be in format
   * "HH:MM", where HH : [00-23] and MM : [00-00] GMT.
   *
   * @var string
   */
  public $startTime;

  /**
   * Defines a schedule that runs on specific days of the week. Specify one or
   * more days. The following options are available: MONDAY, TUESDAY, WEDNESDAY,
   * THURSDAY, FRIDAY, SATURDAY, SUNDAY.
   *
   * Accepted values: FRIDAY, INVALID, MONDAY, SATURDAY, SUNDAY, THURSDAY,
   * TUESDAY, WEDNESDAY
   *
   * @param self::DAY_* $day
   */
  public function setDay($day)
  {
    $this->day = $day;
  }
  /**
   * @return self::DAY_*
   */
  public function getDay()
  {
    return $this->day;
  }
  /**
   * Output only. [Output only] Duration of the time window, automatically
   * chosen to be smallest possible in the given scenario.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Time within the window to start the operations. It must be in format
   * "HH:MM", where HH : [00-23] and MM : [00-00] GMT.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourcePolicyWeeklyCycleDayOfWeek::class, 'Google_Service_Compute_ResourcePolicyWeeklyCycleDayOfWeek');
