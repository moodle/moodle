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

class CloudSqlInstanceBackupProperties extends \Google\Model
{
  /**
   * Output only. The installed database version of the Cloud SQL instance when
   * the backup was taken.
   *
   * @var string
   */
  public $databaseInstalledVersion;
  /**
   * Output only. Whether the backup is a final backup.
   *
   * @var bool
   */
  public $finalBackup;
  /**
   * Output only. The instance creation timestamp.
   *
   * @var string
   */
  public $instanceCreateTime;
  /**
   * Output only. The instance delete timestamp.
   *
   * @var string
   */
  public $instanceDeleteTime;
  /**
   * Output only. The tier (or machine type) for this instance. Example: `db-
   * custom-1-3840`
   *
   * @var string
   */
  public $instanceTier;
  /**
   * Output only. The source instance of the backup. Format:
   * projects/{project}/instances/{instance}
   *
   * @var string
   */
  public $sourceInstance;

  /**
   * Output only. The installed database version of the Cloud SQL instance when
   * the backup was taken.
   *
   * @param string $databaseInstalledVersion
   */
  public function setDatabaseInstalledVersion($databaseInstalledVersion)
  {
    $this->databaseInstalledVersion = $databaseInstalledVersion;
  }
  /**
   * @return string
   */
  public function getDatabaseInstalledVersion()
  {
    return $this->databaseInstalledVersion;
  }
  /**
   * Output only. Whether the backup is a final backup.
   *
   * @param bool $finalBackup
   */
  public function setFinalBackup($finalBackup)
  {
    $this->finalBackup = $finalBackup;
  }
  /**
   * @return bool
   */
  public function getFinalBackup()
  {
    return $this->finalBackup;
  }
  /**
   * Output only. The instance creation timestamp.
   *
   * @param string $instanceCreateTime
   */
  public function setInstanceCreateTime($instanceCreateTime)
  {
    $this->instanceCreateTime = $instanceCreateTime;
  }
  /**
   * @return string
   */
  public function getInstanceCreateTime()
  {
    return $this->instanceCreateTime;
  }
  /**
   * Output only. The instance delete timestamp.
   *
   * @param string $instanceDeleteTime
   */
  public function setInstanceDeleteTime($instanceDeleteTime)
  {
    $this->instanceDeleteTime = $instanceDeleteTime;
  }
  /**
   * @return string
   */
  public function getInstanceDeleteTime()
  {
    return $this->instanceDeleteTime;
  }
  /**
   * Output only. The tier (or machine type) for this instance. Example: `db-
   * custom-1-3840`
   *
   * @param string $instanceTier
   */
  public function setInstanceTier($instanceTier)
  {
    $this->instanceTier = $instanceTier;
  }
  /**
   * @return string
   */
  public function getInstanceTier()
  {
    return $this->instanceTier;
  }
  /**
   * Output only. The source instance of the backup. Format:
   * projects/{project}/instances/{instance}
   *
   * @param string $sourceInstance
   */
  public function setSourceInstance($sourceInstance)
  {
    $this->sourceInstance = $sourceInstance;
  }
  /**
   * @return string
   */
  public function getSourceInstance()
  {
    return $this->sourceInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudSqlInstanceBackupProperties::class, 'Google_Service_Backupdr_CloudSqlInstanceBackupProperties');
