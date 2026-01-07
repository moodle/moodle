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

class StorageDatabasecenterPartnerapiV1mainBackupDRMetadata extends \Google\Model
{
  protected $backupConfigurationType = StorageDatabasecenterPartnerapiV1mainBackupConfiguration::class;
  protected $backupConfigurationDataType = '';
  protected $backupRunType = StorageDatabasecenterPartnerapiV1mainBackupRun::class;
  protected $backupRunDataType = '';
  protected $backupdrConfigurationType = StorageDatabasecenterPartnerapiV1mainBackupDRConfiguration::class;
  protected $backupdrConfigurationDataType = '';
  /**
   * Required. Full resource name of this instance.
   *
   * @var string
   */
  public $fullResourceName;
  /**
   * Required. Last time backup configuration was refreshed.
   *
   * @var string
   */
  public $lastRefreshTime;
  protected $resourceIdType = StorageDatabasecenterPartnerapiV1mainDatabaseResourceId::class;
  protected $resourceIdDataType = '';

  /**
   * Backup configuration for this instance.
   *
   * @param StorageDatabasecenterPartnerapiV1mainBackupConfiguration $backupConfiguration
   */
  public function setBackupConfiguration(StorageDatabasecenterPartnerapiV1mainBackupConfiguration $backupConfiguration)
  {
    $this->backupConfiguration = $backupConfiguration;
  }
  /**
   * @return StorageDatabasecenterPartnerapiV1mainBackupConfiguration
   */
  public function getBackupConfiguration()
  {
    return $this->backupConfiguration;
  }
  /**
   * Latest backup run information for this instance.
   *
   * @param StorageDatabasecenterPartnerapiV1mainBackupRun $backupRun
   */
  public function setBackupRun(StorageDatabasecenterPartnerapiV1mainBackupRun $backupRun)
  {
    $this->backupRun = $backupRun;
  }
  /**
   * @return StorageDatabasecenterPartnerapiV1mainBackupRun
   */
  public function getBackupRun()
  {
    return $this->backupRun;
  }
  /**
   * BackupDR configuration for this instance.
   *
   * @param StorageDatabasecenterPartnerapiV1mainBackupDRConfiguration $backupdrConfiguration
   */
  public function setBackupdrConfiguration(StorageDatabasecenterPartnerapiV1mainBackupDRConfiguration $backupdrConfiguration)
  {
    $this->backupdrConfiguration = $backupdrConfiguration;
  }
  /**
   * @return StorageDatabasecenterPartnerapiV1mainBackupDRConfiguration
   */
  public function getBackupdrConfiguration()
  {
    return $this->backupdrConfiguration;
  }
  /**
   * Required. Full resource name of this instance.
   *
   * @param string $fullResourceName
   */
  public function setFullResourceName($fullResourceName)
  {
    $this->fullResourceName = $fullResourceName;
  }
  /**
   * @return string
   */
  public function getFullResourceName()
  {
    return $this->fullResourceName;
  }
  /**
   * Required. Last time backup configuration was refreshed.
   *
   * @param string $lastRefreshTime
   */
  public function setLastRefreshTime($lastRefreshTime)
  {
    $this->lastRefreshTime = $lastRefreshTime;
  }
  /**
   * @return string
   */
  public function getLastRefreshTime()
  {
    return $this->lastRefreshTime;
  }
  /**
   * Required. Database resource id.
   *
   * @param StorageDatabasecenterPartnerapiV1mainDatabaseResourceId $resourceId
   */
  public function setResourceId(StorageDatabasecenterPartnerapiV1mainDatabaseResourceId $resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return StorageDatabasecenterPartnerapiV1mainDatabaseResourceId
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageDatabasecenterPartnerapiV1mainBackupDRMetadata::class, 'Google_Service_CloudAlloyDBAdmin_StorageDatabasecenterPartnerapiV1mainBackupDRMetadata');
