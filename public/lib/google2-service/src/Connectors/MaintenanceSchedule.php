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

namespace Google\Service\Connectors;

class MaintenanceSchedule extends \Google\Model
{
  /**
   * This field is deprecated, and will be always set to true since reschedule
   * can happen multiple times now. This field should not be removed until all
   * service producers remove this for their customers.
   *
   * @deprecated
   * @var bool
   */
  public $canReschedule;
  /**
   * The scheduled end time for the maintenance.
   *
   * @var string
   */
  public $endTime;
  /**
   * The rollout management policy this maintenance schedule is associated with.
   * When doing reschedule update request, the reschedule should be against this
   * given policy.
   *
   * @var string
   */
  public $rolloutManagementPolicy;
  /**
   * schedule_deadline_time is the time deadline any schedule start time cannot
   * go beyond, including reschedule. It's normally the initial schedule start
   * time plus maintenance window length (1 day or 1 week). Maintenance cannot
   * be scheduled to start beyond this deadline.
   *
   * @var string
   */
  public $scheduleDeadlineTime;
  /**
   * The scheduled start time for the maintenance.
   *
   * @var string
   */
  public $startTime;

  /**
   * This field is deprecated, and will be always set to true since reschedule
   * can happen multiple times now. This field should not be removed until all
   * service producers remove this for their customers.
   *
   * @deprecated
   * @param bool $canReschedule
   */
  public function setCanReschedule($canReschedule)
  {
    $this->canReschedule = $canReschedule;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanReschedule()
  {
    return $this->canReschedule;
  }
  /**
   * The scheduled end time for the maintenance.
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
   * The rollout management policy this maintenance schedule is associated with.
   * When doing reschedule update request, the reschedule should be against this
   * given policy.
   *
   * @param string $rolloutManagementPolicy
   */
  public function setRolloutManagementPolicy($rolloutManagementPolicy)
  {
    $this->rolloutManagementPolicy = $rolloutManagementPolicy;
  }
  /**
   * @return string
   */
  public function getRolloutManagementPolicy()
  {
    return $this->rolloutManagementPolicy;
  }
  /**
   * schedule_deadline_time is the time deadline any schedule start time cannot
   * go beyond, including reschedule. It's normally the initial schedule start
   * time plus maintenance window length (1 day or 1 week). Maintenance cannot
   * be scheduled to start beyond this deadline.
   *
   * @param string $scheduleDeadlineTime
   */
  public function setScheduleDeadlineTime($scheduleDeadlineTime)
  {
    $this->scheduleDeadlineTime = $scheduleDeadlineTime;
  }
  /**
   * @return string
   */
  public function getScheduleDeadlineTime()
  {
    return $this->scheduleDeadlineTime;
  }
  /**
   * The scheduled start time for the maintenance.
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
class_alias(MaintenanceSchedule::class, 'Google_Service_Connectors_MaintenanceSchedule');
