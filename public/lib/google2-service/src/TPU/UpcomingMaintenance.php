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

namespace Google\Service\TPU;

class UpcomingMaintenance extends \Google\Model
{
  /**
   * Unknown maintenance status. Do not use this value.
   */
  public const MAINTENANCE_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * There is pending maintenance.
   */
  public const MAINTENANCE_STATUS_PENDING = 'PENDING';
  /**
   * There is ongoing maintenance on this VM.
   */
  public const MAINTENANCE_STATUS_ONGOING = 'ONGOING';
  /**
   * No type specified. Do not use this value.
   */
  public const TYPE_UNKNOWN_TYPE = 'UNKNOWN_TYPE';
  /**
   * Scheduled maintenance (e.g. maintenance after uptime guarantee is
   * complete).
   */
  public const TYPE_SCHEDULED = 'SCHEDULED';
  /**
   * Unscheduled maintenance (e.g. emergency maintenance during uptime
   * guarantee).
   */
  public const TYPE_UNSCHEDULED = 'UNSCHEDULED';
  /**
   * Indicates if the maintenance can be customer triggered.
   *
   * @var bool
   */
  public $canReschedule;
  /**
   * The latest time for the planned maintenance window to start. This timestamp
   * value is in RFC3339 text format.
   *
   * @var string
   */
  public $latestWindowStartTime;
  /**
   * The status of the maintenance.
   *
   * @var string
   */
  public $maintenanceStatus;
  /**
   * Defines the type of maintenance.
   *
   * @var string
   */
  public $type;
  /**
   * The time by which the maintenance disruption will be completed. This
   * timestamp value is in RFC3339 text format.
   *
   * @var string
   */
  public $windowEndTime;
  /**
   * The current start time of the maintenance window. This timestamp value is
   * in RFC3339 text format.
   *
   * @var string
   */
  public $windowStartTime;

  /**
   * Indicates if the maintenance can be customer triggered.
   *
   * @param bool $canReschedule
   */
  public function setCanReschedule($canReschedule)
  {
    $this->canReschedule = $canReschedule;
  }
  /**
   * @return bool
   */
  public function getCanReschedule()
  {
    return $this->canReschedule;
  }
  /**
   * The latest time for the planned maintenance window to start. This timestamp
   * value is in RFC3339 text format.
   *
   * @param string $latestWindowStartTime
   */
  public function setLatestWindowStartTime($latestWindowStartTime)
  {
    $this->latestWindowStartTime = $latestWindowStartTime;
  }
  /**
   * @return string
   */
  public function getLatestWindowStartTime()
  {
    return $this->latestWindowStartTime;
  }
  /**
   * The status of the maintenance.
   *
   * Accepted values: UNKNOWN, PENDING, ONGOING
   *
   * @param self::MAINTENANCE_STATUS_* $maintenanceStatus
   */
  public function setMaintenanceStatus($maintenanceStatus)
  {
    $this->maintenanceStatus = $maintenanceStatus;
  }
  /**
   * @return self::MAINTENANCE_STATUS_*
   */
  public function getMaintenanceStatus()
  {
    return $this->maintenanceStatus;
  }
  /**
   * Defines the type of maintenance.
   *
   * Accepted values: UNKNOWN_TYPE, SCHEDULED, UNSCHEDULED
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The time by which the maintenance disruption will be completed. This
   * timestamp value is in RFC3339 text format.
   *
   * @param string $windowEndTime
   */
  public function setWindowEndTime($windowEndTime)
  {
    $this->windowEndTime = $windowEndTime;
  }
  /**
   * @return string
   */
  public function getWindowEndTime()
  {
    return $this->windowEndTime;
  }
  /**
   * The current start time of the maintenance window. This timestamp value is
   * in RFC3339 text format.
   *
   * @param string $windowStartTime
   */
  public function setWindowStartTime($windowStartTime)
  {
    $this->windowStartTime = $windowStartTime;
  }
  /**
   * @return string
   */
  public function getWindowStartTime()
  {
    return $this->windowStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpcomingMaintenance::class, 'Google_Service_TPU_UpcomingMaintenance');
