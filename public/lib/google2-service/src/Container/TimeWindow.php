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

namespace Google\Service\Container;

class TimeWindow extends \Google\Model
{
  /**
   * The time that the window ends. The end time should take place after the
   * start time.
   *
   * @var string
   */
  public $endTime;
  protected $maintenanceExclusionOptionsType = MaintenanceExclusionOptions::class;
  protected $maintenanceExclusionOptionsDataType = '';
  /**
   * The time that the window first starts.
   *
   * @var string
   */
  public $startTime;

  /**
   * The time that the window ends. The end time should take place after the
   * start time.
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
   * MaintenanceExclusionOptions provides maintenance exclusion related options.
   *
   * @param MaintenanceExclusionOptions $maintenanceExclusionOptions
   */
  public function setMaintenanceExclusionOptions(MaintenanceExclusionOptions $maintenanceExclusionOptions)
  {
    $this->maintenanceExclusionOptions = $maintenanceExclusionOptions;
  }
  /**
   * @return MaintenanceExclusionOptions
   */
  public function getMaintenanceExclusionOptions()
  {
    return $this->maintenanceExclusionOptions;
  }
  /**
   * The time that the window first starts.
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
class_alias(TimeWindow::class, 'Google_Service_Container_TimeWindow');
