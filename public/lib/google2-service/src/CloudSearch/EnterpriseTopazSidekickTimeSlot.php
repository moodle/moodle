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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickTimeSlot extends \Google\Model
{
  /**
   * Day end time at the user's timezone.
   *
   * @var string
   */
  public $endTimeDay;
  /**
   * Hour and minute of the end time at the user's timezone.
   *
   * @var string
   */
  public $endTimeHourAndMinute;
  /**
   * End time in milliseconds.
   *
   * @var string
   */
  public $endTimeInMillis;
  /**
   * Day start time at user's timezone.
   *
   * @var string
   */
  public $startTimeDay;
  /**
   * Hour and minute of the start time at the user's timezone.
   *
   * @var string
   */
  public $startTimeHourAndMinute;
  /**
   * Start time in milliseconds.
   *
   * @var string
   */
  public $startTimeInMillis;

  /**
   * Day end time at the user's timezone.
   *
   * @param string $endTimeDay
   */
  public function setEndTimeDay($endTimeDay)
  {
    $this->endTimeDay = $endTimeDay;
  }
  /**
   * @return string
   */
  public function getEndTimeDay()
  {
    return $this->endTimeDay;
  }
  /**
   * Hour and minute of the end time at the user's timezone.
   *
   * @param string $endTimeHourAndMinute
   */
  public function setEndTimeHourAndMinute($endTimeHourAndMinute)
  {
    $this->endTimeHourAndMinute = $endTimeHourAndMinute;
  }
  /**
   * @return string
   */
  public function getEndTimeHourAndMinute()
  {
    return $this->endTimeHourAndMinute;
  }
  /**
   * End time in milliseconds.
   *
   * @param string $endTimeInMillis
   */
  public function setEndTimeInMillis($endTimeInMillis)
  {
    $this->endTimeInMillis = $endTimeInMillis;
  }
  /**
   * @return string
   */
  public function getEndTimeInMillis()
  {
    return $this->endTimeInMillis;
  }
  /**
   * Day start time at user's timezone.
   *
   * @param string $startTimeDay
   */
  public function setStartTimeDay($startTimeDay)
  {
    $this->startTimeDay = $startTimeDay;
  }
  /**
   * @return string
   */
  public function getStartTimeDay()
  {
    return $this->startTimeDay;
  }
  /**
   * Hour and minute of the start time at the user's timezone.
   *
   * @param string $startTimeHourAndMinute
   */
  public function setStartTimeHourAndMinute($startTimeHourAndMinute)
  {
    $this->startTimeHourAndMinute = $startTimeHourAndMinute;
  }
  /**
   * @return string
   */
  public function getStartTimeHourAndMinute()
  {
    return $this->startTimeHourAndMinute;
  }
  /**
   * Start time in milliseconds.
   *
   * @param string $startTimeInMillis
   */
  public function setStartTimeInMillis($startTimeInMillis)
  {
    $this->startTimeInMillis = $startTimeInMillis;
  }
  /**
   * @return string
   */
  public function getStartTimeInMillis()
  {
    return $this->startTimeInMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickTimeSlot::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickTimeSlot');
