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

namespace Google\Service\BigQueryDataTransfer;

class ScheduleOptionsV2 extends \Google\Model
{
  protected $eventDrivenScheduleType = EventDrivenSchedule::class;
  protected $eventDrivenScheduleDataType = '';
  protected $manualScheduleType = ManualSchedule::class;
  protected $manualScheduleDataType = '';
  protected $timeBasedScheduleType = TimeBasedSchedule::class;
  protected $timeBasedScheduleDataType = '';

  /**
   * Event driven transfer schedule options. If set, the transfer will be
   * scheduled upon events arrial.
   *
   * @param EventDrivenSchedule $eventDrivenSchedule
   */
  public function setEventDrivenSchedule(EventDrivenSchedule $eventDrivenSchedule)
  {
    $this->eventDrivenSchedule = $eventDrivenSchedule;
  }
  /**
   * @return EventDrivenSchedule
   */
  public function getEventDrivenSchedule()
  {
    return $this->eventDrivenSchedule;
  }
  /**
   * Manual transfer schedule. If set, the transfer run will not be auto-
   * scheduled by the system, unless the client invokes StartManualTransferRuns.
   * This is equivalent to disable_auto_scheduling = true.
   *
   * @param ManualSchedule $manualSchedule
   */
  public function setManualSchedule(ManualSchedule $manualSchedule)
  {
    $this->manualSchedule = $manualSchedule;
  }
  /**
   * @return ManualSchedule
   */
  public function getManualSchedule()
  {
    return $this->manualSchedule;
  }
  /**
   * Time based transfer schedule options. This is the default schedule option.
   *
   * @param TimeBasedSchedule $timeBasedSchedule
   */
  public function setTimeBasedSchedule(TimeBasedSchedule $timeBasedSchedule)
  {
    $this->timeBasedSchedule = $timeBasedSchedule;
  }
  /**
   * @return TimeBasedSchedule
   */
  public function getTimeBasedSchedule()
  {
    return $this->timeBasedSchedule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ScheduleOptionsV2::class, 'Google_Service_BigQueryDataTransfer_ScheduleOptionsV2');
