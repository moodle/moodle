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

namespace Google\Service\Calendar;

class EventDateTime extends \Google\Model
{
  /**
   * The date, in the format "yyyy-mm-dd", if this is an all-day event.
   *
   * @var string
   */
  public $date;
  /**
   * The time, as a combined date-time value (formatted according to RFC3339). A
   * time zone offset is required unless a time zone is explicitly specified in
   * timeZone.
   *
   * @var string
   */
  public $dateTime;
  /**
   * The time zone in which the time is specified. (Formatted as an IANA Time
   * Zone Database name, e.g. "Europe/Zurich".) For recurring events this field
   * is required and specifies the time zone in which the recurrence is
   * expanded. For single events this field is optional and indicates a custom
   * time zone for the event start/end.
   *
   * @var string
   */
  public $timeZone;

  /**
   * The date, in the format "yyyy-mm-dd", if this is an all-day event.
   *
   * @param string $date
   */
  public function setDate($date)
  {
    $this->date = $date;
  }
  /**
   * @return string
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * The time, as a combined date-time value (formatted according to RFC3339). A
   * time zone offset is required unless a time zone is explicitly specified in
   * timeZone.
   *
   * @param string $dateTime
   */
  public function setDateTime($dateTime)
  {
    $this->dateTime = $dateTime;
  }
  /**
   * @return string
   */
  public function getDateTime()
  {
    return $this->dateTime;
  }
  /**
   * The time zone in which the time is specified. (Formatted as an IANA Time
   * Zone Database name, e.g. "Europe/Zurich".) For recurring events this field
   * is required and specifies the time zone in which the recurrence is
   * expanded. For single events this field is optional and indicates a custom
   * time zone for the event start/end.
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
class_alias(EventDateTime::class, 'Google_Service_Calendar_EventDateTime');
