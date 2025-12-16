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

namespace Google\Service\Storagetransfer;

class Schedule extends \Google\Model
{
  protected $endTimeOfDayType = TimeOfDay::class;
  protected $endTimeOfDayDataType = '';
  /**
   * Interval between the start of each scheduled TransferOperation. If
   * unspecified, the default value is 24 hours. This value may not be less than
   * 1 hour.
   *
   * @var string
   */
  public $repeatInterval;
  protected $scheduleEndDateType = Date::class;
  protected $scheduleEndDateDataType = '';
  protected $scheduleStartDateType = Date::class;
  protected $scheduleStartDateDataType = '';
  protected $startTimeOfDayType = TimeOfDay::class;
  protected $startTimeOfDayDataType = '';

  /**
   * The time in UTC that no further transfer operations are scheduled. Combined
   * with schedule_end_date, `end_time_of_day` specifies the end date and time
   * for starting new transfer operations. This field must be greater than or
   * equal to the timestamp corresponding to the combination of
   * schedule_start_date and start_time_of_day, and is subject to the following:
   * * If `end_time_of_day` is not set and `schedule_end_date` is set, then a
   * default value of `23:59:59` is used for `end_time_of_day`. * If
   * `end_time_of_day` is set and `schedule_end_date` is not set, then
   * INVALID_ARGUMENT is returned.
   *
   * @param TimeOfDay $endTimeOfDay
   */
  public function setEndTimeOfDay(TimeOfDay $endTimeOfDay)
  {
    $this->endTimeOfDay = $endTimeOfDay;
  }
  /**
   * @return TimeOfDay
   */
  public function getEndTimeOfDay()
  {
    return $this->endTimeOfDay;
  }
  /**
   * Interval between the start of each scheduled TransferOperation. If
   * unspecified, the default value is 24 hours. This value may not be less than
   * 1 hour.
   *
   * @param string $repeatInterval
   */
  public function setRepeatInterval($repeatInterval)
  {
    $this->repeatInterval = $repeatInterval;
  }
  /**
   * @return string
   */
  public function getRepeatInterval()
  {
    return $this->repeatInterval;
  }
  /**
   * The last day a transfer runs. Date boundaries are determined relative to
   * UTC time. A job runs once per 24 hours within the following guidelines: *
   * If `schedule_end_date` and schedule_start_date are the same and in the
   * future relative to UTC, the transfer is executed only one time. * If
   * `schedule_end_date` is later than `schedule_start_date` and
   * `schedule_end_date` is in the future relative to UTC, the job runs each day
   * at start_time_of_day through `schedule_end_date`.
   *
   * @param Date $scheduleEndDate
   */
  public function setScheduleEndDate(Date $scheduleEndDate)
  {
    $this->scheduleEndDate = $scheduleEndDate;
  }
  /**
   * @return Date
   */
  public function getScheduleEndDate()
  {
    return $this->scheduleEndDate;
  }
  /**
   * Required. The start date of a transfer. Date boundaries are determined
   * relative to UTC time. If `schedule_start_date` and start_time_of_day are in
   * the past relative to the job's creation time, the transfer starts the day
   * after you schedule the transfer request. **Note:** When starting jobs at or
   * near midnight UTC it is possible that a job starts later than expected. For
   * example, if you send an outbound request on June 1 one millisecond prior to
   * midnight UTC and the Storage Transfer Service server receives the request
   * on June 2, then it creates a TransferJob with `schedule_start_date` set to
   * June 2 and a `start_time_of_day` set to midnight UTC. The first scheduled
   * TransferOperation takes place on June 3 at midnight UTC.
   *
   * @param Date $scheduleStartDate
   */
  public function setScheduleStartDate(Date $scheduleStartDate)
  {
    $this->scheduleStartDate = $scheduleStartDate;
  }
  /**
   * @return Date
   */
  public function getScheduleStartDate()
  {
    return $this->scheduleStartDate;
  }
  /**
   * The time in UTC that a transfer job is scheduled to run. Transfers may
   * start later than this time. If `start_time_of_day` is not specified: * One-
   * time transfers run immediately. * Recurring transfers run immediately, and
   * each day at midnight UTC, through schedule_end_date. If `start_time_of_day`
   * is specified: * One-time transfers run at the specified time. * Recurring
   * transfers run at the specified time each day, through `schedule_end_date`.
   *
   * @param TimeOfDay $startTimeOfDay
   */
  public function setStartTimeOfDay(TimeOfDay $startTimeOfDay)
  {
    $this->startTimeOfDay = $startTimeOfDay;
  }
  /**
   * @return TimeOfDay
   */
  public function getStartTimeOfDay()
  {
    return $this->startTimeOfDay;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Schedule::class, 'Google_Service_Storagetransfer_Schedule');
