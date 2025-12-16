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

namespace Google\Service\OracleDatabase;

class DatabaseProperties extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const STATE_DATABASE_LIFECYCLE_STATE_UNSPECIFIED = 'DATABASE_LIFECYCLE_STATE_UNSPECIFIED';
  /**
   * Indicates that the resource is in provisioning state.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * Indicates that the resource is in available state.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * Indicates that the resource is in updating state.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Indicates that the resource is in backup in progress state.
   */
  public const STATE_BACKUP_IN_PROGRESS = 'BACKUP_IN_PROGRESS';
  /**
   * Indicates that the resource is in upgrading state.
   */
  public const STATE_UPGRADING = 'UPGRADING';
  /**
   * Indicates that the resource is in converting state.
   */
  public const STATE_CONVERTING = 'CONVERTING';
  /**
   * Indicates that the resource is in terminating state.
   */
  public const STATE_TERMINATING = 'TERMINATING';
  /**
   * Indicates that the resource is in terminated state.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  /**
   * Indicates that the resource is in restore failed state.
   */
  public const STATE_RESTORE_FAILED = 'RESTORE_FAILED';
  /**
   * Indicates that the resource is in failed state.
   */
  public const STATE_FAILED = 'FAILED';
  protected $databaseManagementConfigType = DatabaseManagementConfig::class;
  protected $databaseManagementConfigDataType = '';
  protected $dbBackupConfigType = DbBackupConfig::class;
  protected $dbBackupConfigDataType = '';
  /**
   * Required. The Oracle Database version.
   *
   * @var string
   */
  public $dbVersion;
  /**
   * Output only. State of the Database.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The Database Management config.
   *
   * @param DatabaseManagementConfig $databaseManagementConfig
   */
  public function setDatabaseManagementConfig(DatabaseManagementConfig $databaseManagementConfig)
  {
    $this->databaseManagementConfig = $databaseManagementConfig;
  }
  /**
   * @return DatabaseManagementConfig
   */
  public function getDatabaseManagementConfig()
  {
    return $this->databaseManagementConfig;
  }
  /**
   * Optional. Backup options for the Database.
   *
   * @param DbBackupConfig $dbBackupConfig
   */
  public function setDbBackupConfig(DbBackupConfig $dbBackupConfig)
  {
    $this->dbBackupConfig = $dbBackupConfig;
  }
  /**
   * @return DbBackupConfig
   */
  public function getDbBackupConfig()
  {
    return $this->dbBackupConfig;
  }
  /**
   * Required. The Oracle Database version.
   *
   * @param string $dbVersion
   */
  public function setDbVersion($dbVersion)
  {
    $this->dbVersion = $dbVersion;
  }
  /**
   * @return string
   */
  public function getDbVersion()
  {
    return $this->dbVersion;
  }
  /**
   * Output only. State of the Database.
   *
   * Accepted values: DATABASE_LIFECYCLE_STATE_UNSPECIFIED, PROVISIONING,
   * AVAILABLE, UPDATING, BACKUP_IN_PROGRESS, UPGRADING, CONVERTING,
   * TERMINATING, TERMINATED, RESTORE_FAILED, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseProperties::class, 'Google_Service_OracleDatabase_DatabaseProperties');
