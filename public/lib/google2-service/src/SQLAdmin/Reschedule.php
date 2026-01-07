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

namespace Google\Service\SQLAdmin;

class Reschedule extends \Google\Model
{
  public const RESCHEDULE_TYPE_RESCHEDULE_TYPE_UNSPECIFIED = 'RESCHEDULE_TYPE_UNSPECIFIED';
  /**
   * Reschedules maintenance to happen now (within 5 minutes).
   */
  public const RESCHEDULE_TYPE_IMMEDIATE = 'IMMEDIATE';
  /**
   * Reschedules maintenance to occur within one week from the originally
   * scheduled day and time.
   */
  public const RESCHEDULE_TYPE_NEXT_AVAILABLE_WINDOW = 'NEXT_AVAILABLE_WINDOW';
  /**
   * Reschedules maintenance to a specific time and day.
   */
  public const RESCHEDULE_TYPE_SPECIFIC_TIME = 'SPECIFIC_TIME';
  /**
   * Required. The type of the reschedule.
   *
   * @var string
   */
  public $rescheduleType;
  /**
   * Optional. Timestamp when the maintenance shall be rescheduled to if
   * reschedule_type=SPECIFIC_TIME, in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @var string
   */
  public $scheduleTime;

  /**
   * Required. The type of the reschedule.
   *
   * Accepted values: RESCHEDULE_TYPE_UNSPECIFIED, IMMEDIATE,
   * NEXT_AVAILABLE_WINDOW, SPECIFIC_TIME
   *
   * @param self::RESCHEDULE_TYPE_* $rescheduleType
   */
  public function setRescheduleType($rescheduleType)
  {
    $this->rescheduleType = $rescheduleType;
  }
  /**
   * @return self::RESCHEDULE_TYPE_*
   */
  public function getRescheduleType()
  {
    return $this->rescheduleType;
  }
  /**
   * Optional. Timestamp when the maintenance shall be rescheduled to if
   * reschedule_type=SPECIFIC_TIME, in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @param string $scheduleTime
   */
  public function setScheduleTime($scheduleTime)
  {
    $this->scheduleTime = $scheduleTime;
  }
  /**
   * @return string
   */
  public function getScheduleTime()
  {
    return $this->scheduleTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Reschedule::class, 'Google_Service_SQLAdmin_Reschedule');
