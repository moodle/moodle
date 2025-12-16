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

class BackupWindow extends \Google\Model
{
  /**
   * Required. The hour of day (1-24) when the window end for e.g. if value of
   * end hour of day is 10 that mean backup window end time is 10:00. End hour
   * of day should be greater than start hour of day. 0 <= start_hour_of_day <
   * end_hour_of_day <= 24 End hour of day is not include in backup window that
   * mean if end_hour_of_day= 10 jobs should start before 10:00.
   *
   * @var int
   */
  public $endHourOfDay;
  /**
   * Required. The hour of day (0-23) when the window starts for e.g. if value
   * of start hour of day is 6 that mean backup window start at 6:00.
   *
   * @var int
   */
  public $startHourOfDay;

  /**
   * Required. The hour of day (1-24) when the window end for e.g. if value of
   * end hour of day is 10 that mean backup window end time is 10:00. End hour
   * of day should be greater than start hour of day. 0 <= start_hour_of_day <
   * end_hour_of_day <= 24 End hour of day is not include in backup window that
   * mean if end_hour_of_day= 10 jobs should start before 10:00.
   *
   * @param int $endHourOfDay
   */
  public function setEndHourOfDay($endHourOfDay)
  {
    $this->endHourOfDay = $endHourOfDay;
  }
  /**
   * @return int
   */
  public function getEndHourOfDay()
  {
    return $this->endHourOfDay;
  }
  /**
   * Required. The hour of day (0-23) when the window starts for e.g. if value
   * of start hour of day is 6 that mean backup window start at 6:00.
   *
   * @param int $startHourOfDay
   */
  public function setStartHourOfDay($startHourOfDay)
  {
    $this->startHourOfDay = $startHourOfDay;
  }
  /**
   * @return int
   */
  public function getStartHourOfDay()
  {
    return $this->startHourOfDay;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupWindow::class, 'Google_Service_Backupdr_BackupWindow');
