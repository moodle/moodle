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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1SqlDatabaseSystemSpec extends \Google\Model
{
  /**
   * Version of the database engine.
   *
   * @var string
   */
  public $databaseVersion;
  /**
   * Host of the SQL database enum InstanceHost { UNDEFINED = 0; SELF_HOSTED =
   * 1; CLOUD_SQL = 2; AMAZON_RDS = 3; AZURE_SQL = 4; } Host of the enclousing
   * database instance.
   *
   * @var string
   */
  public $instanceHost;
  /**
   * SQL Database Engine. enum SqlEngine { UNDEFINED = 0; MY_SQL = 1;
   * POSTGRE_SQL = 2; SQL_SERVER = 3; } Engine of the enclosing database
   * instance.
   *
   * @var string
   */
  public $sqlEngine;

  /**
   * Version of the database engine.
   *
   * @param string $databaseVersion
   */
  public function setDatabaseVersion($databaseVersion)
  {
    $this->databaseVersion = $databaseVersion;
  }
  /**
   * @return string
   */
  public function getDatabaseVersion()
  {
    return $this->databaseVersion;
  }
  /**
   * Host of the SQL database enum InstanceHost { UNDEFINED = 0; SELF_HOSTED =
   * 1; CLOUD_SQL = 2; AMAZON_RDS = 3; AZURE_SQL = 4; } Host of the enclousing
   * database instance.
   *
   * @param string $instanceHost
   */
  public function setInstanceHost($instanceHost)
  {
    $this->instanceHost = $instanceHost;
  }
  /**
   * @return string
   */
  public function getInstanceHost()
  {
    return $this->instanceHost;
  }
  /**
   * SQL Database Engine. enum SqlEngine { UNDEFINED = 0; MY_SQL = 1;
   * POSTGRE_SQL = 2; SQL_SERVER = 3; } Engine of the enclosing database
   * instance.
   *
   * @param string $sqlEngine
   */
  public function setSqlEngine($sqlEngine)
  {
    $this->sqlEngine = $sqlEngine;
  }
  /**
   * @return string
   */
  public function getSqlEngine()
  {
    return $this->sqlEngine;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1SqlDatabaseSystemSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1SqlDatabaseSystemSpec');
