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

namespace Google\Service\CloudRedis;

class ResourceMaintenanceInfo extends \Google\Collection
{
  protected $collection_key = 'denyMaintenanceSchedules';
  protected $denyMaintenanceSchedulesType = ResourceMaintenanceDenySchedule::class;
  protected $denyMaintenanceSchedulesDataType = 'array';
  protected $maintenanceScheduleType = ResourceMaintenanceSchedule::class;
  protected $maintenanceScheduleDataType = '';
  /**
   * Optional. Current Maintenance version of the database resource. Example:
   * "MYSQL_8_0_41.R20250531.01_15"
   *
   * @var string
   */
  public $maintenanceVersion;

  /**
   * Optional. List of Deny maintenance period for the database resource.
   *
   * @param ResourceMaintenanceDenySchedule[] $denyMaintenanceSchedules
   */
  public function setDenyMaintenanceSchedules($denyMaintenanceSchedules)
  {
    $this->denyMaintenanceSchedules = $denyMaintenanceSchedules;
  }
  /**
   * @return ResourceMaintenanceDenySchedule[]
   */
  public function getDenyMaintenanceSchedules()
  {
    return $this->denyMaintenanceSchedules;
  }
  /**
   * Optional. Maintenance window for the database resource.
   *
   * @param ResourceMaintenanceSchedule $maintenanceSchedule
   */
  public function setMaintenanceSchedule(ResourceMaintenanceSchedule $maintenanceSchedule)
  {
    $this->maintenanceSchedule = $maintenanceSchedule;
  }
  /**
   * @return ResourceMaintenanceSchedule
   */
  public function getMaintenanceSchedule()
  {
    return $this->maintenanceSchedule;
  }
  /**
   * Optional. Current Maintenance version of the database resource. Example:
   * "MYSQL_8_0_41.R20250531.01_15"
   *
   * @param string $maintenanceVersion
   */
  public function setMaintenanceVersion($maintenanceVersion)
  {
    $this->maintenanceVersion = $maintenanceVersion;
  }
  /**
   * @return string
   */
  public function getMaintenanceVersion()
  {
    return $this->maintenanceVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceMaintenanceInfo::class, 'Google_Service_CloudRedis_ResourceMaintenanceInfo');
