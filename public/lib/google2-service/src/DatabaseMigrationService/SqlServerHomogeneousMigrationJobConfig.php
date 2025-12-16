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

namespace Google\Service\DatabaseMigrationService;

class SqlServerHomogeneousMigrationJobConfig extends \Google\Collection
{
  protected $collection_key = 'databaseBackups';
  /**
   * Required. Pattern that describes the default backup naming strategy. The
   * specified pattern should ensure lexicographical order of backups. The
   * pattern must define one of the following capture group sets: Capture group
   * set #1 yy/yyyy - year, 2 or 4 digits mm - month number, 1-12 dd - day of
   * month, 1-31 hh - hour of day, 00-23 mi - minutes, 00-59 ss - seconds, 00-59
   * Example: For backup file TestDB_20230802_155400.trn, use pattern:
   * (?.*)_backup_(?\d{4})(?\d{2})(?\d{2})_(?\d{2})(?\d{2})(?\d{2}).trn Capture
   * group set #2 timestamp - unix timestamp Example: For backup file
   * TestDB.1691448254.trn, use pattern: (?.*)\.(?\d*).trn or (?.*)\.(?\d*).trn
   *
   * @var string
   */
  public $backupFilePattern;
  protected $dagConfigType = SqlServerDagConfig::class;
  protected $dagConfigDataType = '';
  protected $databaseBackupsType = SqlServerDatabaseBackup::class;
  protected $databaseBackupsDataType = 'array';
  /**
   * Optional. Promote databases when ready.
   *
   * @var bool
   */
  public $promoteWhenReady;
  /**
   * Optional. Enable differential backups.
   *
   * @var bool
   */
  public $useDiffBackup;

  /**
   * Required. Pattern that describes the default backup naming strategy. The
   * specified pattern should ensure lexicographical order of backups. The
   * pattern must define one of the following capture group sets: Capture group
   * set #1 yy/yyyy - year, 2 or 4 digits mm - month number, 1-12 dd - day of
   * month, 1-31 hh - hour of day, 00-23 mi - minutes, 00-59 ss - seconds, 00-59
   * Example: For backup file TestDB_20230802_155400.trn, use pattern:
   * (?.*)_backup_(?\d{4})(?\d{2})(?\d{2})_(?\d{2})(?\d{2})(?\d{2}).trn Capture
   * group set #2 timestamp - unix timestamp Example: For backup file
   * TestDB.1691448254.trn, use pattern: (?.*)\.(?\d*).trn or (?.*)\.(?\d*).trn
   *
   * @param string $backupFilePattern
   */
  public function setBackupFilePattern($backupFilePattern)
  {
    $this->backupFilePattern = $backupFilePattern;
  }
  /**
   * @return string
   */
  public function getBackupFilePattern()
  {
    return $this->backupFilePattern;
  }
  /**
   * Optional. Configuration for distributed availability group (DAG) for the
   * SQL Server homogeneous migration.
   *
   * @param SqlServerDagConfig $dagConfig
   */
  public function setDagConfig(SqlServerDagConfig $dagConfig)
  {
    $this->dagConfig = $dagConfig;
  }
  /**
   * @return SqlServerDagConfig
   */
  public function getDagConfig()
  {
    return $this->dagConfig;
  }
  /**
   * Required. Backup details per database in Cloud Storage.
   *
   * @param SqlServerDatabaseBackup[] $databaseBackups
   */
  public function setDatabaseBackups($databaseBackups)
  {
    $this->databaseBackups = $databaseBackups;
  }
  /**
   * @return SqlServerDatabaseBackup[]
   */
  public function getDatabaseBackups()
  {
    return $this->databaseBackups;
  }
  /**
   * Optional. Promote databases when ready.
   *
   * @param bool $promoteWhenReady
   */
  public function setPromoteWhenReady($promoteWhenReady)
  {
    $this->promoteWhenReady = $promoteWhenReady;
  }
  /**
   * @return bool
   */
  public function getPromoteWhenReady()
  {
    return $this->promoteWhenReady;
  }
  /**
   * Optional. Enable differential backups.
   *
   * @param bool $useDiffBackup
   */
  public function setUseDiffBackup($useDiffBackup)
  {
    $this->useDiffBackup = $useDiffBackup;
  }
  /**
   * @return bool
   */
  public function getUseDiffBackup()
  {
    return $this->useDiffBackup;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlServerHomogeneousMigrationJobConfig::class, 'Google_Service_DatabaseMigrationService_SqlServerHomogeneousMigrationJobConfig');
