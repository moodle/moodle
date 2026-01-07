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

namespace Google\Service\CCAIPlatform;

class EventTemplate extends \Google\Model
{
  /**
   * Required. Fixed duration in minutes of this event.
   *
   * @var int
   */
  public $durationMinutes;
  /**
   * Required. Unique ID of this template.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. Maximum number of minutes after the beginning of a shift that
   * this event can start.
   *
   * @var int
   */
  public $maximumMinutesAfterShiftStart;
  /**
   * Optional. Minimum number of minutes after the beginning of a shift that
   * this event can start.
   *
   * @var int
   */
  public $minimumMinutesAfterShiftStart;
  /**
   * Required. The time increment (in minutes) used to generate the set of
   * possible event start times between `minimum_minutes_after_shift_start` and
   * `maximum_minutes_after_shift_start`. For example, if the minimum minutes
   * after shift start are 30, maximum minutes after shift start are 45, and the
   * start time increment is 5 minutes, the event can take place 30, 35, 40, or
   * 45 minutes after the start of the shift.
   *
   * @var int
   */
  public $startTimeIncrementMinutes;

  /**
   * Required. Fixed duration in minutes of this event.
   *
   * @param int $durationMinutes
   */
  public function setDurationMinutes($durationMinutes)
  {
    $this->durationMinutes = $durationMinutes;
  }
  /**
   * @return int
   */
  public function getDurationMinutes()
  {
    return $this->durationMinutes;
  }
  /**
   * Required. Unique ID of this template.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Optional. Maximum number of minutes after the beginning of a shift that
   * this event can start.
   *
   * @param int $maximumMinutesAfterShiftStart
   */
  public function setMaximumMinutesAfterShiftStart($maximumMinutesAfterShiftStart)
  {
    $this->maximumMinutesAfterShiftStart = $maximumMinutesAfterShiftStart;
  }
  /**
   * @return int
   */
  public function getMaximumMinutesAfterShiftStart()
  {
    return $this->maximumMinutesAfterShiftStart;
  }
  /**
   * Optional. Minimum number of minutes after the beginning of a shift that
   * this event can start.
   *
   * @param int $minimumMinutesAfterShiftStart
   */
  public function setMinimumMinutesAfterShiftStart($minimumMinutesAfterShiftStart)
  {
    $this->minimumMinutesAfterShiftStart = $minimumMinutesAfterShiftStart;
  }
  /**
   * @return int
   */
  public function getMinimumMinutesAfterShiftStart()
  {
    return $this->minimumMinutesAfterShiftStart;
  }
  /**
   * Required. The time increment (in minutes) used to generate the set of
   * possible event start times between `minimum_minutes_after_shift_start` and
   * `maximum_minutes_after_shift_start`. For example, if the minimum minutes
   * after shift start are 30, maximum minutes after shift start are 45, and the
   * start time increment is 5 minutes, the event can take place 30, 35, 40, or
   * 45 minutes after the start of the shift.
   *
   * @param int $startTimeIncrementMinutes
   */
  public function setStartTimeIncrementMinutes($startTimeIncrementMinutes)
  {
    $this->startTimeIncrementMinutes = $startTimeIncrementMinutes;
  }
  /**
   * @return int
   */
  public function getStartTimeIncrementMinutes()
  {
    return $this->startTimeIncrementMinutes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventTemplate::class, 'Google_Service_CCAIPlatform_EventTemplate');
