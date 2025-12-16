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

namespace Google\Service\CloudDeploy;

class TimeWindows extends \Google\Collection
{
  protected $collection_key = 'weeklyWindows';
  protected $oneTimeWindowsType = OneTimeWindow::class;
  protected $oneTimeWindowsDataType = 'array';
  /**
   * Required. The time zone in IANA format [IANA Time Zone
   * Database](https://www.iana.org/time-zones) (e.g. America/New_York).
   *
   * @var string
   */
  public $timeZone;
  protected $weeklyWindowsType = WeeklyWindow::class;
  protected $weeklyWindowsDataType = 'array';

  /**
   * Optional. One-time windows within which actions are restricted.
   *
   * @param OneTimeWindow[] $oneTimeWindows
   */
  public function setOneTimeWindows($oneTimeWindows)
  {
    $this->oneTimeWindows = $oneTimeWindows;
  }
  /**
   * @return OneTimeWindow[]
   */
  public function getOneTimeWindows()
  {
    return $this->oneTimeWindows;
  }
  /**
   * Required. The time zone in IANA format [IANA Time Zone
   * Database](https://www.iana.org/time-zones) (e.g. America/New_York).
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
  /**
   * Optional. Recurring weekly windows within which actions are restricted.
   *
   * @param WeeklyWindow[] $weeklyWindows
   */
  public function setWeeklyWindows($weeklyWindows)
  {
    $this->weeklyWindows = $weeklyWindows;
  }
  /**
   * @return WeeklyWindow[]
   */
  public function getWeeklyWindows()
  {
    return $this->weeklyWindows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimeWindows::class, 'Google_Service_CloudDeploy_TimeWindows');
