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

namespace Google\Service\WorkloadManager;

class UpcomingMaintenanceEvent extends \Google\Model
{
  /**
   * Optional. End time
   *
   * @var string
   */
  public $endTime;
  /**
   * Optional. Maintenance status
   *
   * @var string
   */
  public $maintenanceStatus;
  /**
   * Optional. Instance maintenance behavior. Could be "MIGRATE" or "TERMINATE".
   *
   * @var string
   */
  public $onHostMaintenance;
  /**
   * Optional. Start time
   *
   * @var string
   */
  public $startTime;
  /**
   * Optional. Type
   *
   * @var string
   */
  public $type;

  /**
   * Optional. End time
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
   * Optional. Maintenance status
   *
   * @param string $maintenanceStatus
   */
  public function setMaintenanceStatus($maintenanceStatus)
  {
    $this->maintenanceStatus = $maintenanceStatus;
  }
  /**
   * @return string
   */
  public function getMaintenanceStatus()
  {
    return $this->maintenanceStatus;
  }
  /**
   * Optional. Instance maintenance behavior. Could be "MIGRATE" or "TERMINATE".
   *
   * @param string $onHostMaintenance
   */
  public function setOnHostMaintenance($onHostMaintenance)
  {
    $this->onHostMaintenance = $onHostMaintenance;
  }
  /**
   * @return string
   */
  public function getOnHostMaintenance()
  {
    return $this->onHostMaintenance;
  }
  /**
   * Optional. Start time
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
  /**
   * Optional. Type
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpcomingMaintenanceEvent::class, 'Google_Service_WorkloadManager_UpcomingMaintenanceEvent');
