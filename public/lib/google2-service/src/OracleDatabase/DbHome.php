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

class DbHome extends \Google\Model
{
  protected $databaseType = Database::class;
  protected $databaseDataType = '';
  /**
   * Required. A valid Oracle Database version. For a list of supported
   * versions, use the ListDbVersions operation.
   *
   * @var string
   */
  public $dbVersion;
  /**
   * Optional. The display name for the Database Home. The name does not have to
   * be unique within your project.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Whether unified auditing is enabled for the Database Home.
   *
   * @var bool
   */
  public $isUnifiedAuditingEnabled;

  /**
   * Required. The Database resource.
   *
   * @param Database $database
   */
  public function setDatabase(Database $database)
  {
    $this->database = $database;
  }
  /**
   * @return Database
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Required. A valid Oracle Database version. For a list of supported
   * versions, use the ListDbVersions operation.
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
   * Optional. The display name for the Database Home. The name does not have to
   * be unique within your project.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Whether unified auditing is enabled for the Database Home.
   *
   * @param bool $isUnifiedAuditingEnabled
   */
  public function setIsUnifiedAuditingEnabled($isUnifiedAuditingEnabled)
  {
    $this->isUnifiedAuditingEnabled = $isUnifiedAuditingEnabled;
  }
  /**
   * @return bool
   */
  public function getIsUnifiedAuditingEnabled()
  {
    return $this->isUnifiedAuditingEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbHome::class, 'Google_Service_OracleDatabase_DbHome');
