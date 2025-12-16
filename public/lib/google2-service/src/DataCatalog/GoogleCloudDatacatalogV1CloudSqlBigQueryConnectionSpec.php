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

class GoogleCloudDatacatalogV1CloudSqlBigQueryConnectionSpec extends \Google\Model
{
  /**
   * Unspecified database type.
   */
  public const TYPE_DATABASE_TYPE_UNSPECIFIED = 'DATABASE_TYPE_UNSPECIFIED';
  /**
   * Cloud SQL for PostgreSQL.
   */
  public const TYPE_POSTGRES = 'POSTGRES';
  /**
   * Cloud SQL for MySQL.
   */
  public const TYPE_MYSQL = 'MYSQL';
  /**
   * Database name.
   *
   * @var string
   */
  public $database;
  /**
   * Cloud SQL instance ID in the format of `project:location:instance`.
   *
   * @var string
   */
  public $instanceId;
  /**
   * Type of the Cloud SQL database.
   *
   * @var string
   */
  public $type;

  /**
   * Database name.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Cloud SQL instance ID in the format of `project:location:instance`.
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
  /**
   * Type of the Cloud SQL database.
   *
   * Accepted values: DATABASE_TYPE_UNSPECIFIED, POSTGRES, MYSQL
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1CloudSqlBigQueryConnectionSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1CloudSqlBigQueryConnectionSpec');
