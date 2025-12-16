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

class TimeOfDay extends \Google\Model
{
  /**
   * Hours of a day in 24 hour format. Must be greater than or equal to 0 and
   * typically must be less than or equal to 23. An API may choose to allow the
   * value "24:00:00" for scenarios like business closing time.
   *
   * @var int
   */
  public $hours;
  /**
   * Minutes of an hour. Must be greater than or equal to 0 and less than or
   * equal to 59.
   *
   * @var int
   */
  public $minutes;
  /**
   * Fractions of seconds, in nanoseconds. Must be greater than or equal to 0
   * and less than or equal to 999,999,999.
   *
   * @var int
   */
  public $nanos;
  /**
   * Seconds of a minute. Must be greater than or equal to 0 and typically must
   * be less than or equal to 59. An API may allow the value 60 if it allows
   * leap-seconds.
   *
   * @var int
   */
  public $seconds;

  /**
   * Hours of a day in 24 hour format. Must be greater than or equal to 0 and
   * typically must be less than or equal to 23. An API may choose to allow the
   * value "24:00:00" for scenarios like business closing time.
   *
   * @param int $hours
   */
  public function setHours($hours)
  {
    $this->hours = $hours;
  }
  /**
   * @return int
   */
  public function getHours()
  {
    return $this->hours;
  }
  /**
   * Minutes of an hour. Must be greater than or equal to 0 and less than or
   * equal to 59.
   *
   * @param int $minutes
   */
  public function setMinutes($minutes)
  {
    $this->minutes = $minutes;
  }
  /**
   * @return int
   */
  public function getMinutes()
  {
    return $this->minutes;
  }
  /**
   * Fractions of seconds, in nanoseconds. Must be greater than or equal to 0
   * and less than or equal to 999,999,999.
   *
   * @param int $nanos
   */
  public function setNanos($nanos)
  {
    $this->nanos = $nanos;
  }
  /**
   * @return int
   */
  public function getNanos()
  {
    return $this->nanos;
  }
  /**
   * Seconds of a minute. Must be greater than or equal to 0 and typically must
   * be less than or equal to 59. An API may allow the value 60 if it allows
   * leap-seconds.
   *
   * @param int $seconds
   */
  public function setSeconds($seconds)
  {
    $this->seconds = $seconds;
  }
  /**
   * @return int
   */
  public function getSeconds()
  {
    return $this->seconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimeOfDay::class, 'Google_Service_CCAIPlatform_TimeOfDay');
