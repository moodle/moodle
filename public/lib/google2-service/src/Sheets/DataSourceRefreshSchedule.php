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

namespace Google\Service\Sheets;

class DataSourceRefreshSchedule extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const REFRESH_SCOPE_DATA_SOURCE_REFRESH_SCOPE_UNSPECIFIED = 'DATA_SOURCE_REFRESH_SCOPE_UNSPECIFIED';
  /**
   * Refreshes all data sources and their associated data source objects in the
   * spreadsheet.
   */
  public const REFRESH_SCOPE_ALL_DATA_SOURCES = 'ALL_DATA_SOURCES';
  protected $dailyScheduleType = DataSourceRefreshDailySchedule::class;
  protected $dailyScheduleDataType = '';
  /**
   * True if the refresh schedule is enabled, or false otherwise.
   *
   * @var bool
   */
  public $enabled;
  protected $monthlyScheduleType = DataSourceRefreshMonthlySchedule::class;
  protected $monthlyScheduleDataType = '';
  protected $nextRunType = Interval::class;
  protected $nextRunDataType = '';
  /**
   * The scope of the refresh. Must be ALL_DATA_SOURCES.
   *
   * @var string
   */
  public $refreshScope;
  protected $weeklyScheduleType = DataSourceRefreshWeeklySchedule::class;
  protected $weeklyScheduleDataType = '';

  /**
   * Daily refresh schedule.
   *
   * @param DataSourceRefreshDailySchedule $dailySchedule
   */
  public function setDailySchedule(DataSourceRefreshDailySchedule $dailySchedule)
  {
    $this->dailySchedule = $dailySchedule;
  }
  /**
   * @return DataSourceRefreshDailySchedule
   */
  public function getDailySchedule()
  {
    return $this->dailySchedule;
  }
  /**
   * True if the refresh schedule is enabled, or false otherwise.
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
   * Monthly refresh schedule.
   *
   * @param DataSourceRefreshMonthlySchedule $monthlySchedule
   */
  public function setMonthlySchedule(DataSourceRefreshMonthlySchedule $monthlySchedule)
  {
    $this->monthlySchedule = $monthlySchedule;
  }
  /**
   * @return DataSourceRefreshMonthlySchedule
   */
  public function getMonthlySchedule()
  {
    return $this->monthlySchedule;
  }
  /**
   * Output only. The time interval of the next run.
   *
   * @param Interval $nextRun
   */
  public function setNextRun(Interval $nextRun)
  {
    $this->nextRun = $nextRun;
  }
  /**
   * @return Interval
   */
  public function getNextRun()
  {
    return $this->nextRun;
  }
  /**
   * The scope of the refresh. Must be ALL_DATA_SOURCES.
   *
   * Accepted values: DATA_SOURCE_REFRESH_SCOPE_UNSPECIFIED, ALL_DATA_SOURCES
   *
   * @param self::REFRESH_SCOPE_* $refreshScope
   */
  public function setRefreshScope($refreshScope)
  {
    $this->refreshScope = $refreshScope;
  }
  /**
   * @return self::REFRESH_SCOPE_*
   */
  public function getRefreshScope()
  {
    return $this->refreshScope;
  }
  /**
   * Weekly refresh schedule.
   *
   * @param DataSourceRefreshWeeklySchedule $weeklySchedule
   */
  public function setWeeklySchedule(DataSourceRefreshWeeklySchedule $weeklySchedule)
  {
    $this->weeklySchedule = $weeklySchedule;
  }
  /**
   * @return DataSourceRefreshWeeklySchedule
   */
  public function getWeeklySchedule()
  {
    return $this->weeklySchedule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSourceRefreshSchedule::class, 'Google_Service_Sheets_DataSourceRefreshSchedule');
