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

class Database extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const OPS_INSIGHTS_STATUS_OPERATIONS_INSIGHTS_STATUS_UNSPECIFIED = 'OPERATIONS_INSIGHTS_STATUS_UNSPECIFIED';
  /**
   * Indicates that the operations insights are being enabled.
   */
  public const OPS_INSIGHTS_STATUS_ENABLING = 'ENABLING';
  /**
   * Indicates that the operations insights are enabled.
   */
  public const OPS_INSIGHTS_STATUS_ENABLED = 'ENABLED';
  /**
   * Indicates that the operations insights are being disabled.
   */
  public const OPS_INSIGHTS_STATUS_DISABLING = 'DISABLING';
  /**
   * Indicates that the operations insights are not enabled.
   */
  public const OPS_INSIGHTS_STATUS_NOT_ENABLED = 'NOT_ENABLED';
  /**
   * Indicates that the operations insights failed to enable.
   */
  public const OPS_INSIGHTS_STATUS_FAILED_ENABLING = 'FAILED_ENABLING';
  /**
   * Indicates that the operations insights failed to disable.
   */
  public const OPS_INSIGHTS_STATUS_FAILED_DISABLING = 'FAILED_DISABLING';
  /**
   * Required. The password for the default ADMIN user.
   *
   * @var string
   */
  public $adminPassword;
  /**
   * Optional. The character set for the database. The default is AL32UTF8.
   *
   * @var string
   */
  public $characterSet;
  /**
   * Output only. The date and time that the Database was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The database ID of the Database.
   *
   * @var string
   */
  public $databaseId;
  /**
   * Optional. The name of the DbHome resource associated with the Database.
   *
   * @var string
   */
  public $dbHomeName;
  /**
   * Optional. The database name. The name must begin with an alphabetic
   * character and can contain a maximum of eight alphanumeric characters.
   * Special characters are not permitted.
   *
   * @var string
   */
  public $dbName;
  /**
   * Optional. The DB_UNIQUE_NAME of the Oracle Database being backed up.
   *
   * @var string
   */
  public $dbUniqueName;
  /**
   * Output only. The GCP Oracle zone where the Database is created.
   *
   * @var string
   */
  public $gcpOracleZone;
  /**
   * Identifier. The name of the Database resource in the following format:
   * projects/{project}/locations/{region}/databases/{database}
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The national character set for the database. The default is
   * AL16UTF16.
   *
   * @var string
   */
  public $ncharacterSet;
  /**
   * Output only. HTTPS link to OCI resources exposed to Customer via UI
   * Interface.
   *
   * @var string
   */
  public $ociUrl;
  /**
   * Output only. The Status of Operations Insights for this Database.
   *
   * @var string
   */
  public $opsInsightsStatus;
  protected $propertiesType = DatabaseProperties::class;
  protected $propertiesDataType = '';
  /**
   * Optional. The TDE wallet password for the database.
   *
   * @var string
   */
  public $tdeWalletPassword;

  /**
   * Required. The password for the default ADMIN user.
   *
   * @param string $adminPassword
   */
  public function setAdminPassword($adminPassword)
  {
    $this->adminPassword = $adminPassword;
  }
  /**
   * @return string
   */
  public function getAdminPassword()
  {
    return $this->adminPassword;
  }
  /**
   * Optional. The character set for the database. The default is AL32UTF8.
   *
   * @param string $characterSet
   */
  public function setCharacterSet($characterSet)
  {
    $this->characterSet = $characterSet;
  }
  /**
   * @return string
   */
  public function getCharacterSet()
  {
    return $this->characterSet;
  }
  /**
   * Output only. The date and time that the Database was created.
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
   * Optional. The database ID of the Database.
   *
   * @param string $databaseId
   */
  public function setDatabaseId($databaseId)
  {
    $this->databaseId = $databaseId;
  }
  /**
   * @return string
   */
  public function getDatabaseId()
  {
    return $this->databaseId;
  }
  /**
   * Optional. The name of the DbHome resource associated with the Database.
   *
   * @param string $dbHomeName
   */
  public function setDbHomeName($dbHomeName)
  {
    $this->dbHomeName = $dbHomeName;
  }
  /**
   * @return string
   */
  public function getDbHomeName()
  {
    return $this->dbHomeName;
  }
  /**
   * Optional. The database name. The name must begin with an alphabetic
   * character and can contain a maximum of eight alphanumeric characters.
   * Special characters are not permitted.
   *
   * @param string $dbName
   */
  public function setDbName($dbName)
  {
    $this->dbName = $dbName;
  }
  /**
   * @return string
   */
  public function getDbName()
  {
    return $this->dbName;
  }
  /**
   * Optional. The DB_UNIQUE_NAME of the Oracle Database being backed up.
   *
   * @param string $dbUniqueName
   */
  public function setDbUniqueName($dbUniqueName)
  {
    $this->dbUniqueName = $dbUniqueName;
  }
  /**
   * @return string
   */
  public function getDbUniqueName()
  {
    return $this->dbUniqueName;
  }
  /**
   * Output only. The GCP Oracle zone where the Database is created.
   *
   * @param string $gcpOracleZone
   */
  public function setGcpOracleZone($gcpOracleZone)
  {
    $this->gcpOracleZone = $gcpOracleZone;
  }
  /**
   * @return string
   */
  public function getGcpOracleZone()
  {
    return $this->gcpOracleZone;
  }
  /**
   * Identifier. The name of the Database resource in the following format:
   * projects/{project}/locations/{region}/databases/{database}
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. The national character set for the database. The default is
   * AL16UTF16.
   *
   * @param string $ncharacterSet
   */
  public function setNcharacterSet($ncharacterSet)
  {
    $this->ncharacterSet = $ncharacterSet;
  }
  /**
   * @return string
   */
  public function getNcharacterSet()
  {
    return $this->ncharacterSet;
  }
  /**
   * Output only. HTTPS link to OCI resources exposed to Customer via UI
   * Interface.
   *
   * @param string $ociUrl
   */
  public function setOciUrl($ociUrl)
  {
    $this->ociUrl = $ociUrl;
  }
  /**
   * @return string
   */
  public function getOciUrl()
  {
    return $this->ociUrl;
  }
  /**
   * Output only. The Status of Operations Insights for this Database.
   *
   * Accepted values: OPERATIONS_INSIGHTS_STATUS_UNSPECIFIED, ENABLING, ENABLED,
   * DISABLING, NOT_ENABLED, FAILED_ENABLING, FAILED_DISABLING
   *
   * @param self::OPS_INSIGHTS_STATUS_* $opsInsightsStatus
   */
  public function setOpsInsightsStatus($opsInsightsStatus)
  {
    $this->opsInsightsStatus = $opsInsightsStatus;
  }
  /**
   * @return self::OPS_INSIGHTS_STATUS_*
   */
  public function getOpsInsightsStatus()
  {
    return $this->opsInsightsStatus;
  }
  /**
   * Optional. The properties of the Database.
   *
   * @param DatabaseProperties $properties
   */
  public function setProperties(DatabaseProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return DatabaseProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Optional. The TDE wallet password for the database.
   *
   * @param string $tdeWalletPassword
   */
  public function setTdeWalletPassword($tdeWalletPassword)
  {
    $this->tdeWalletPassword = $tdeWalletPassword;
  }
  /**
   * @return string
   */
  public function getTdeWalletPassword()
  {
    return $this->tdeWalletPassword;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Database::class, 'Google_Service_OracleDatabase_Database');
