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

namespace Google\Service\Backupdr;

class DataSourceBackupApplianceApplication extends \Google\Model
{
  /**
   * Appliance Id of the Backup Appliance.
   *
   * @var string
   */
  public $applianceId;
  /**
   * The appid field of the application within the Backup Appliance.
   *
   * @var string
   */
  public $applicationId;
  /**
   * The name of the Application as known to the Backup Appliance.
   *
   * @var string
   */
  public $applicationName;
  /**
   * Appliance name.
   *
   * @var string
   */
  public $backupAppliance;
  /**
   * Hostid of the application host.
   *
   * @var string
   */
  public $hostId;
  /**
   * Hostname of the host where the application is running.
   *
   * @var string
   */
  public $hostname;
  /**
   * The type of the application. e.g. VMBackup
   *
   * @var string
   */
  public $type;

  /**
   * Appliance Id of the Backup Appliance.
   *
   * @param string $applianceId
   */
  public function setApplianceId($applianceId)
  {
    $this->applianceId = $applianceId;
  }
  /**
   * @return string
   */
  public function getApplianceId()
  {
    return $this->applianceId;
  }
  /**
   * The appid field of the application within the Backup Appliance.
   *
   * @param string $applicationId
   */
  public function setApplicationId($applicationId)
  {
    $this->applicationId = $applicationId;
  }
  /**
   * @return string
   */
  public function getApplicationId()
  {
    return $this->applicationId;
  }
  /**
   * The name of the Application as known to the Backup Appliance.
   *
   * @param string $applicationName
   */
  public function setApplicationName($applicationName)
  {
    $this->applicationName = $applicationName;
  }
  /**
   * @return string
   */
  public function getApplicationName()
  {
    return $this->applicationName;
  }
  /**
   * Appliance name.
   *
   * @param string $backupAppliance
   */
  public function setBackupAppliance($backupAppliance)
  {
    $this->backupAppliance = $backupAppliance;
  }
  /**
   * @return string
   */
  public function getBackupAppliance()
  {
    return $this->backupAppliance;
  }
  /**
   * Hostid of the application host.
   *
   * @param string $hostId
   */
  public function setHostId($hostId)
  {
    $this->hostId = $hostId;
  }
  /**
   * @return string
   */
  public function getHostId()
  {
    return $this->hostId;
  }
  /**
   * Hostname of the host where the application is running.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * The type of the application. e.g. VMBackup
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
class_alias(DataSourceBackupApplianceApplication::class, 'Google_Service_Backupdr_DataSourceBackupApplianceApplication');
