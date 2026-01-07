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

namespace Google\Service\WorkloadManager;

class DatabaseProperties extends \Google\Model
{
  /**
   * unspecified
   */
  public const DATABASE_TYPE_DATABASE_TYPE_UNSPECIFIED = 'DATABASE_TYPE_UNSPECIFIED';
  /**
   * SAP HANA
   */
  public const DATABASE_TYPE_HANA = 'HANA';
  /**
   * SAP MAX_DB
   */
  public const DATABASE_TYPE_MAX_DB = 'MAX_DB';
  /**
   * IBM DB2
   */
  public const DATABASE_TYPE_DB2 = 'DB2';
  /**
   * Oracle Database
   */
  public const DATABASE_TYPE_ORACLE = 'ORACLE';
  /**
   * Microsoft SQL Server
   */
  public const DATABASE_TYPE_SQLSERVER = 'SQLSERVER';
  /**
   * SAP Sybase ASE
   */
  public const DATABASE_TYPE_ASE = 'ASE';
  protected $backupPropertiesType = BackupProperties::class;
  protected $backupPropertiesDataType = '';
  /**
   * Output only. Type of the database. HANA, DB2, etc.
   *
   * @var string
   */
  public $databaseType;

  /**
   * Output only. Backup properties.
   *
   * @param BackupProperties $backupProperties
   */
  public function setBackupProperties(BackupProperties $backupProperties)
  {
    $this->backupProperties = $backupProperties;
  }
  /**
   * @return BackupProperties
   */
  public function getBackupProperties()
  {
    return $this->backupProperties;
  }
  /**
   * Output only. Type of the database. HANA, DB2, etc.
   *
   * Accepted values: DATABASE_TYPE_UNSPECIFIED, HANA, MAX_DB, DB2, ORACLE,
   * SQLSERVER, ASE
   *
   * @param self::DATABASE_TYPE_* $databaseType
   */
  public function setDatabaseType($databaseType)
  {
    $this->databaseType = $databaseType;
  }
  /**
   * @return self::DATABASE_TYPE_*
   */
  public function getDatabaseType()
  {
    return $this->databaseType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseProperties::class, 'Google_Service_WorkloadManager_DatabaseProperties');
