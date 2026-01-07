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

namespace Google\Service\NetAppFiles;

class MonthlySchedule extends \Google\Model
{
  /**
   * Set the day or days of the month to make a snapshot (1-31). Accepts a comma
   * separated number of days. Defaults to '1'.
   *
   * @var string
   */
  public $daysOfMonth;
  /**
   * Set the hour to start the snapshot (0-23), defaults to midnight (0).
   *
   * @var 
   */
  public $hour;
  /**
   * Set the minute of the hour to start the snapshot (0-59), defaults to the
   * top of the hour (0).
   *
   * @var 
   */
  public $minute;
  /**
   * The maximum number of Snapshots to keep for the hourly schedule
   *
   * @var 
   */
  public $snapshotsToKeep;

  /**
   * Set the day or days of the month to make a snapshot (1-31). Accepts a comma
   * separated number of days. Defaults to '1'.
   *
   * @param string $daysOfMonth
   */
  public function setDaysOfMonth($daysOfMonth)
  {
    $this->daysOfMonth = $daysOfMonth;
  }
  /**
   * @return string
   */
  public function getDaysOfMonth()
  {
    return $this->daysOfMonth;
  }
  public function setHour($hour)
  {
    $this->hour = $hour;
  }
  public function getHour()
  {
    return $this->hour;
  }
  public function setMinute($minute)
  {
    $this->minute = $minute;
  }
  public function getMinute()
  {
    return $this->minute;
  }
  public function setSnapshotsToKeep($snapshotsToKeep)
  {
    $this->snapshotsToKeep = $snapshotsToKeep;
  }
  public function getSnapshotsToKeep()
  {
    return $this->snapshotsToKeep;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MonthlySchedule::class, 'Google_Service_NetAppFiles_MonthlySchedule');
