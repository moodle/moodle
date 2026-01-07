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

namespace Google\Service\CloudAlloyDBAdmin;

class MaintenanceUpdatePolicy extends \Google\Collection
{
  protected $collection_key = 'maintenanceWindows';
  protected $denyMaintenancePeriodsType = DenyMaintenancePeriod::class;
  protected $denyMaintenancePeriodsDataType = 'array';
  protected $maintenanceWindowsType = MaintenanceWindow::class;
  protected $maintenanceWindowsDataType = 'array';

  /**
   * Periods to deny maintenance. Currently limited to 1.
   *
   * @param DenyMaintenancePeriod[] $denyMaintenancePeriods
   */
  public function setDenyMaintenancePeriods($denyMaintenancePeriods)
  {
    $this->denyMaintenancePeriods = $denyMaintenancePeriods;
  }
  /**
   * @return DenyMaintenancePeriod[]
   */
  public function getDenyMaintenancePeriods()
  {
    return $this->denyMaintenancePeriods;
  }
  /**
   * Preferred windows to perform maintenance. Currently limited to 1.
   *
   * @param MaintenanceWindow[] $maintenanceWindows
   */
  public function setMaintenanceWindows($maintenanceWindows)
  {
    $this->maintenanceWindows = $maintenanceWindows;
  }
  /**
   * @return MaintenanceWindow[]
   */
  public function getMaintenanceWindows()
  {
    return $this->maintenanceWindows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MaintenanceUpdatePolicy::class, 'Google_Service_CloudAlloyDBAdmin_MaintenanceUpdatePolicy');
