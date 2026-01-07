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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1MaintenanceUpdatePolicy extends \Google\Collection
{
  /**
   * Unspecified maintenance channel.
   */
  public const MAINTENANCE_CHANNEL_MAINTENANCE_CHANNEL_UNSPECIFIED = 'MAINTENANCE_CHANNEL_UNSPECIFIED';
  /**
   * Receive 1 weeks notice before maintenance occurs
   */
  public const MAINTENANCE_CHANNEL_WEEK1 = 'WEEK1';
  /**
   * Receive 2 weeks notice before maintenance occurs
   */
  public const MAINTENANCE_CHANNEL_WEEK2 = 'WEEK2';
  protected $collection_key = 'maintenanceWindows';
  /**
   * Optional. Maintenance channel to specify relative scheduling for
   * maintenance.
   *
   * @var string
   */
  public $maintenanceChannel;
  protected $maintenanceWindowsType = GoogleCloudApigeeV1MaintenanceUpdatePolicyMaintenanceWindow::class;
  protected $maintenanceWindowsDataType = 'array';

  /**
   * Optional. Maintenance channel to specify relative scheduling for
   * maintenance.
   *
   * Accepted values: MAINTENANCE_CHANNEL_UNSPECIFIED, WEEK1, WEEK2
   *
   * @param self::MAINTENANCE_CHANNEL_* $maintenanceChannel
   */
  public function setMaintenanceChannel($maintenanceChannel)
  {
    $this->maintenanceChannel = $maintenanceChannel;
  }
  /**
   * @return self::MAINTENANCE_CHANNEL_*
   */
  public function getMaintenanceChannel()
  {
    return $this->maintenanceChannel;
  }
  /**
   * Optional. Preferred windows to perform maintenance. Currently limited to 1.
   *
   * @param GoogleCloudApigeeV1MaintenanceUpdatePolicyMaintenanceWindow[] $maintenanceWindows
   */
  public function setMaintenanceWindows($maintenanceWindows)
  {
    $this->maintenanceWindows = $maintenanceWindows;
  }
  /**
   * @return GoogleCloudApigeeV1MaintenanceUpdatePolicyMaintenanceWindow[]
   */
  public function getMaintenanceWindows()
  {
    return $this->maintenanceWindows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1MaintenanceUpdatePolicy::class, 'Google_Service_Apigee_GoogleCloudApigeeV1MaintenanceUpdatePolicy');
