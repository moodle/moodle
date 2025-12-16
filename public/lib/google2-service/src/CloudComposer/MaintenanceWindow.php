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

namespace Google\Service\CloudComposer;

class MaintenanceWindow extends \Google\Model
{
  /**
   * Required. Maintenance window end time. It is used only to calculate the
   * duration of the maintenance window. The value for end-time must be in the
   * future, relative to `start_time`.
   *
   * @var string
   */
  public $endTime;
  /**
   * Required. Maintenance window recurrence. Format is a subset of
   * [RFC-5545](https://tools.ietf.org/html/rfc5545) `RRULE`. The only allowed
   * values for `FREQ` field are `FREQ=DAILY` and `FREQ=WEEKLY;BYDAY=...`
   * Example values: `FREQ=WEEKLY;BYDAY=TU,WE`, `FREQ=DAILY`.
   *
   * @var string
   */
  public $recurrence;
  /**
   * Required. Start time of the first recurrence of the maintenance window.
   *
   * @var string
   */
  public $startTime;

  /**
   * Required. Maintenance window end time. It is used only to calculate the
   * duration of the maintenance window. The value for end-time must be in the
   * future, relative to `start_time`.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Required. Maintenance window recurrence. Format is a subset of
   * [RFC-5545](https://tools.ietf.org/html/rfc5545) `RRULE`. The only allowed
   * values for `FREQ` field are `FREQ=DAILY` and `FREQ=WEEKLY;BYDAY=...`
   * Example values: `FREQ=WEEKLY;BYDAY=TU,WE`, `FREQ=DAILY`.
   *
   * @param string $recurrence
   */
  public function setRecurrence($recurrence)
  {
    $this->recurrence = $recurrence;
  }
  /**
   * @return string
   */
  public function getRecurrence()
  {
    return $this->recurrence;
  }
  /**
   * Required. Start time of the first recurrence of the maintenance window.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MaintenanceWindow::class, 'Google_Service_CloudComposer_MaintenanceWindow');
