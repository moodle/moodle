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

namespace Google\Service\DataFusion;

class MaintenancePolicy extends \Google\Model
{
  protected $maintenanceExclusionWindowType = TimeWindow::class;
  protected $maintenanceExclusionWindowDataType = '';
  protected $maintenanceWindowType = MaintenanceWindow::class;
  protected $maintenanceWindowDataType = '';

  /**
   * Optional. The maintenance exclusion window of the instance.
   *
   * @param TimeWindow $maintenanceExclusionWindow
   */
  public function setMaintenanceExclusionWindow(TimeWindow $maintenanceExclusionWindow)
  {
    $this->maintenanceExclusionWindow = $maintenanceExclusionWindow;
  }
  /**
   * @return TimeWindow
   */
  public function getMaintenanceExclusionWindow()
  {
    return $this->maintenanceExclusionWindow;
  }
  /**
   * Optional. The maintenance window of the instance.
   *
   * @param MaintenanceWindow $maintenanceWindow
   */
  public function setMaintenanceWindow(MaintenanceWindow $maintenanceWindow)
  {
    $this->maintenanceWindow = $maintenanceWindow;
  }
  /**
   * @return MaintenanceWindow
   */
  public function getMaintenanceWindow()
  {
    return $this->maintenanceWindow;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MaintenancePolicy::class, 'Google_Service_DataFusion_MaintenancePolicy');
