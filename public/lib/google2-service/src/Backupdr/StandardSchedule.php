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

namespace Google\Service\Backupdr;

class StandardSchedule extends \Google\Collection
{
  /**
   * recurrence type not set
   */
  public const RECURRENCE_TYPE_RECURRENCE_TYPE_UNSPECIFIED = 'RECURRENCE_TYPE_UNSPECIFIED';
  /**
   * The `BackupRule` is to be applied hourly.
   */
  public const RECURRENCE_TYPE_HOURLY = 'HOURLY';
  /**
   * The `BackupRule` is to be applied daily.
   */
  public const RECURRENCE_TYPE_DAILY = 'DAILY';
  /**
   * The `BackupRule` is to be applied weekly.
   */
  public const RECURRENCE_TYPE_WEEKLY = 'WEEKLY';
  /**
   * The `BackupRule` is to be applied monthly.
   */
  public const RECURRENCE_TYPE_MONTHLY = 'MONTHLY';
  /**
   * The `BackupRule` is to be applied yearly.
   */
  public const RECURRENCE_TYPE_YEARLY = 'YEARLY';
  protected $collection_key = 'months';
  protected $backupWindowType = BackupWindow::class;
  protected $backupWindowDataType = '';
  /**
   * Optional. Specifies days of months like 1, 5, or 14 on which jobs will run.
   * Values for `days_of_month` are only applicable for `recurrence_type`,
   * `MONTHLY` and `YEARLY`. A validation error will occur if other values are
   * supplied.
   *
   * @var int[]
   */
  public $daysOfMonth;
  /**
   * Optional. Specifies days of week like, MONDAY or TUESDAY, on which jobs
   * will run. This is required for `recurrence_type`, `WEEKLY` and is not
   * applicable otherwise. A validation error will occur if a value is supplied
   * and `recurrence_type` is not `WEEKLY`.
   *
   * @var string[]
   */
  public $daysOfWeek;
  /**
   * Optional. Specifies frequency for hourly backups. A hourly frequency of 2
   * means jobs will run every 2 hours from start time till end time defined.
   * This is required for `recurrence_type`, `HOURLY` and is not applicable
   * otherwise. A validation error will occur if a value is supplied and
   * `recurrence_type` is not `HOURLY`. Value of hourly frequency should be
   * between 4 and 23. Reason for limit : We found that there is bandwidth
   * limitation of 3GB/S for GMI while taking a backup and 5GB/S while doing a
   * restore. Given the amount of parallel backups and restore we are targeting,
   * this will potentially take the backup time to mins and hours (in worst case
   * scenario).
   *
   * @var int
   */
  public $hourlyFrequency;
  /**
   * Optional. Specifies the months of year, like `FEBRUARY` and/or `MAY`, on
   * which jobs will run. This field is only applicable when `recurrence_type`
   * is `YEARLY`. A validation error will occur if other values are supplied.
   *
   * @var string[]
   */
  public $months;
  /**
   * Required. Specifies the `RecurrenceType` for the schedule.
   *
   * @var string
   */
  public $recurrenceType;
  /**
   * Required. The time zone to be used when interpreting the schedule. The
   * value of this field must be a time zone name from the IANA tz database. See
   * https://en.wikipedia.org/wiki/List_of_tz_database_time_zones for the list
   * of valid timezone names. For e.g., Europe/Paris.
   *
   * @var string
   */
  public $timeZone;
  protected $weekDayOfMonthType = WeekDayOfMonth::class;
  protected $weekDayOfMonthDataType = '';

  /**
   * Required. A BackupWindow defines the window of day during which backup jobs
   * will run. Jobs are queued at the beginning of the window and will be marked
   * as `NOT_RUN` if they do not start by the end of the window. Note: running
   * jobs will not be cancelled at the end of the window.
   *
   * @param BackupWindow $backupWindow
   */
  public function setBackupWindow(BackupWindow $backupWindow)
  {
    $this->backupWindow = $backupWindow;
  }
  /**
   * @return BackupWindow
   */
  public function getBackupWindow()
  {
    return $this->backupWindow;
  }
  /**
   * Optional. Specifies days of months like 1, 5, or 14 on which jobs will run.
   * Values for `days_of_month` are only applicable for `recurrence_type`,
   * `MONTHLY` and `YEARLY`. A validation error will occur if other values are
   * supplied.
   *
   * @param int[] $daysOfMonth
   */
  public function setDaysOfMonth($daysOfMonth)
  {
    $this->daysOfMonth = $daysOfMonth;
  }
  /**
   * @return int[]
   */
  public function getDaysOfMonth()
  {
    return $this->daysOfMonth;
  }
  /**
   * Optional. Specifies days of week like, MONDAY or TUESDAY, on which jobs
   * will run. This is required for `recurrence_type`, `WEEKLY` and is not
   * applicable otherwise. A validation error will occur if a value is supplied
   * and `recurrence_type` is not `WEEKLY`.
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
   * Optional. Specifies frequency for hourly backups. A hourly frequency of 2
   * means jobs will run every 2 hours from start time till end time defined.
   * This is required for `recurrence_type`, `HOURLY` and is not applicable
   * otherwise. A validation error will occur if a value is supplied and
   * `recurrence_type` is not `HOURLY`. Value of hourly frequency should be
   * between 4 and 23. Reason for limit : We found that there is bandwidth
   * limitation of 3GB/S for GMI while taking a backup and 5GB/S while doing a
   * restore. Given the amount of parallel backups and restore we are targeting,
   * this will potentially take the backup time to mins and hours (in worst case
   * scenario).
   *
   * @param int $hourlyFrequency
   */
  public function setHourlyFrequency($hourlyFrequency)
  {
    $this->hourlyFrequency = $hourlyFrequency;
  }
  /**
   * @return int
   */
  public function getHourlyFrequency()
  {
    return $this->hourlyFrequency;
  }
  /**
   * Optional. Specifies the months of year, like `FEBRUARY` and/or `MAY`, on
   * which jobs will run. This field is only applicable when `recurrence_type`
   * is `YEARLY`. A validation error will occur if other values are supplied.
   *
   * @param string[] $months
   */
  public function setMonths($months)
  {
    $this->months = $months;
  }
  /**
   * @return string[]
   */
  public function getMonths()
  {
    return $this->months;
  }
  /**
   * Required. Specifies the `RecurrenceType` for the schedule.
   *
   * Accepted values: RECURRENCE_TYPE_UNSPECIFIED, HOURLY, DAILY, WEEKLY,
   * MONTHLY, YEARLY
   *
   * @param self::RECURRENCE_TYPE_* $recurrenceType
   */
  public function setRecurrenceType($recurrenceType)
  {
    $this->recurrenceType = $recurrenceType;
  }
  /**
   * @return self::RECURRENCE_TYPE_*
   */
  public function getRecurrenceType()
  {
    return $this->recurrenceType;
  }
  /**
   * Required. The time zone to be used when interpreting the schedule. The
   * value of this field must be a time zone name from the IANA tz database. See
   * https://en.wikipedia.org/wiki/List_of_tz_database_time_zones for the list
   * of valid timezone names. For e.g., Europe/Paris.
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
   * Optional. Specifies a week day of the month like, FIRST SUNDAY or LAST
   * MONDAY, on which jobs will run. This will be specified by two fields in
   * `WeekDayOfMonth`, one for the day, e.g. `MONDAY`, and one for the week,
   * e.g. `LAST`. This field is only applicable for `recurrence_type`, `MONTHLY`
   * and `YEARLY`. A validation error will occur if other values are supplied.
   *
   * @param WeekDayOfMonth $weekDayOfMonth
   */
  public function setWeekDayOfMonth(WeekDayOfMonth $weekDayOfMonth)
  {
    $this->weekDayOfMonth = $weekDayOfMonth;
  }
  /**
   * @return WeekDayOfMonth
   */
  public function getWeekDayOfMonth()
  {
    return $this->weekDayOfMonth;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StandardSchedule::class, 'Google_Service_Backupdr_StandardSchedule');
