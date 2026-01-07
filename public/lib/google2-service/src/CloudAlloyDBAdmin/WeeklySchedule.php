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

namespace Google\Service\CloudAlloyDBAdmin;

class WeeklySchedule extends \Google\Collection
{
  protected $collection_key = 'startTimes';
  /**
   * The days of the week to perform a backup. If this field is left empty, the
   * default of every day of the week is used.
   *
   * @var string[]
   */
  public $daysOfWeek;
  protected $startTimesType = GoogleTypeTimeOfDay::class;
  protected $startTimesDataType = 'array';

  /**
   * The days of the week to perform a backup. If this field is left empty, the
   * default of every day of the week is used.
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
   * The times during the day to start a backup. The start times are assumed to
   * be in UTC and to be an exact hour (e.g., 04:00:00). If no start times are
   * provided, a single fixed start time is chosen arbitrarily.
   *
   * @param GoogleTypeTimeOfDay[] $startTimes
   */
  public function setStartTimes($startTimes)
  {
    $this->startTimes = $startTimes;
  }
  /**
   * @return GoogleTypeTimeOfDay[]
   */
  public function getStartTimes()
  {
    return $this->startTimes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WeeklySchedule::class, 'Google_Service_CloudAlloyDBAdmin_WeeklySchedule');
