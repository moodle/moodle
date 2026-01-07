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

class SnapshotPolicy extends \Google\Model
{
  protected $dailyScheduleType = DailySchedule::class;
  protected $dailyScheduleDataType = '';
  /**
   * If enabled, make snapshots automatically according to the schedules.
   * Default is false.
   *
   * @var bool
   */
  public $enabled;
  protected $hourlyScheduleType = HourlySchedule::class;
  protected $hourlyScheduleDataType = '';
  protected $monthlyScheduleType = MonthlySchedule::class;
  protected $monthlyScheduleDataType = '';
  protected $weeklyScheduleType = WeeklySchedule::class;
  protected $weeklyScheduleDataType = '';

  /**
   * Daily schedule policy.
   *
   * @param DailySchedule $dailySchedule
   */
  public function setDailySchedule(DailySchedule $dailySchedule)
  {
    $this->dailySchedule = $dailySchedule;
  }
  /**
   * @return DailySchedule
   */
  public function getDailySchedule()
  {
    return $this->dailySchedule;
  }
  /**
   * If enabled, make snapshots automatically according to the schedules.
   * Default is false.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Hourly schedule policy.
   *
   * @param HourlySchedule $hourlySchedule
   */
  public function setHourlySchedule(HourlySchedule $hourlySchedule)
  {
    $this->hourlySchedule = $hourlySchedule;
  }
  /**
   * @return HourlySchedule
   */
  public function getHourlySchedule()
  {
    return $this->hourlySchedule;
  }
  /**
   * Monthly schedule policy.
   *
   * @param MonthlySchedule $monthlySchedule
   */
  public function setMonthlySchedule(MonthlySchedule $monthlySchedule)
  {
    $this->monthlySchedule = $monthlySchedule;
  }
  /**
   * @return MonthlySchedule
   */
  public function getMonthlySchedule()
  {
    return $this->monthlySchedule;
  }
  /**
   * Weekly schedule policy.
   *
   * @param WeeklySchedule $weeklySchedule
   */
  public function setWeeklySchedule(WeeklySchedule $weeklySchedule)
  {
    $this->weeklySchedule = $weeklySchedule;
  }
  /**
   * @return WeeklySchedule
   */
  public function getWeeklySchedule()
  {
    return $this->weeklySchedule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SnapshotPolicy::class, 'Google_Service_NetAppFiles_SnapshotPolicy');
