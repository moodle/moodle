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

class BackupApplianceBackupConfig extends \Google\Model
{
  /**
   * The name of the application.
   *
   * @var string
   */
  public $applicationName;
  /**
   * The ID of the backup appliance.
   *
   * @var string
   */
  public $backupApplianceId;
  /**
   * The name of the backup appliance.
   *
   * @var string
   */
  public $backupApplianceName;
  /**
   * The name of the host where the application is running.
   *
   * @var string
   */
  public $hostName;
  /**
   * The ID of the SLA of this application.
   *
   * @var string
   */
  public $slaId;
  /**
   * The name of the SLP associated with the application.
   *
   * @var string
   */
  public $slpName;
  /**
   * The name of the SLT associated with the application.
   *
   * @var string
   */
  public $sltName;

  /**
   * The name of the application.
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
   * The ID of the backup appliance.
   *
   * @param string $backupApplianceId
   */
  public function setBackupApplianceId($backupApplianceId)
  {
    $this->backupApplianceId = $backupApplianceId;
  }
  /**
   * @return string
   */
  public function getBackupApplianceId()
  {
    return $this->backupApplianceId;
  }
  /**
   * The name of the backup appliance.
   *
   * @param string $backupApplianceName
   */
  public function setBackupApplianceName($backupApplianceName)
  {
    $this->backupApplianceName = $backupApplianceName;
  }
  /**
   * @return string
   */
  public function getBackupApplianceName()
  {
    return $this->backupApplianceName;
  }
  /**
   * The name of the host where the application is running.
   *
   * @param string $hostName
   */
  public function setHostName($hostName)
  {
    $this->hostName = $hostName;
  }
  /**
   * @return string
   */
  public function getHostName()
  {
    return $this->hostName;
  }
  /**
   * The ID of the SLA of this application.
   *
   * @param string $slaId
   */
  public function setSlaId($slaId)
  {
    $this->slaId = $slaId;
  }
  /**
   * @return string
   */
  public function getSlaId()
  {
    return $this->slaId;
  }
  /**
   * The name of the SLP associated with the application.
   *
   * @param string $slpName
   */
  public function setSlpName($slpName)
  {
    $this->slpName = $slpName;
  }
  /**
   * @return string
   */
  public function getSlpName()
  {
    return $this->slpName;
  }
  /**
   * The name of the SLT associated with the application.
   *
   * @param string $sltName
   */
  public function setSltName($sltName)
  {
    $this->sltName = $sltName;
  }
  /**
   * @return string
   */
  public function getSltName()
  {
    return $this->sltName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupApplianceBackupConfig::class, 'Google_Service_Backupdr_BackupApplianceBackupConfig');
