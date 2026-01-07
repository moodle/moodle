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

class ClusterMaintenancePolicy extends \Google\Collection
{
  protected $collection_key = 'weeklyMaintenanceWindow';
  /**
   * Output only. The time when the policy was created i.e. Maintenance Window
   * or Deny Period was assigned.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time when the policy was updated i.e. Maintenance Window
   * or Deny Period was updated.
   *
   * @var string
   */
  public $updateTime;
  protected $weeklyMaintenanceWindowType = ClusterWeeklyMaintenanceWindow::class;
  protected $weeklyMaintenanceWindowDataType = 'array';

  /**
   * Output only. The time when the policy was created i.e. Maintenance Window
   * or Deny Period was assigned.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The time when the policy was updated i.e. Maintenance Window
   * or Deny Period was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Optional. Maintenance window that is applied to resources covered by this
   * policy. Minimum 1. For the current version, the maximum number of
   * weekly_maintenance_window is expected to be one.
   *
   * @param ClusterWeeklyMaintenanceWindow[] $weeklyMaintenanceWindow
   */
  public function setWeeklyMaintenanceWindow($weeklyMaintenanceWindow)
  {
    $this->weeklyMaintenanceWindow = $weeklyMaintenanceWindow;
  }
  /**
   * @return ClusterWeeklyMaintenanceWindow[]
   */
  public function getWeeklyMaintenanceWindow()
  {
    return $this->weeklyMaintenanceWindow;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterMaintenancePolicy::class, 'Google_Service_CloudRedis_ClusterMaintenancePolicy');
