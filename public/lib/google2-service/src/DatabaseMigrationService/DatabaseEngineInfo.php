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

class DatabaseEngineInfo extends \Google\Model
{
  /**
   * The source database engine of the migration job is unknown.
   */
  public const ENGINE_DATABASE_ENGINE_UNSPECIFIED = 'DATABASE_ENGINE_UNSPECIFIED';
  /**
   * The source engine is MySQL.
   */
  public const ENGINE_MYSQL = 'MYSQL';
  /**
   * The source engine is PostgreSQL.
   */
  public const ENGINE_POSTGRESQL = 'POSTGRESQL';
  /**
   * The source engine is SQL Server.
   */
  public const ENGINE_SQLSERVER = 'SQLSERVER';
  /**
   * The source engine is Oracle.
   */
  public const ENGINE_ORACLE = 'ORACLE';
  /**
   * Required. Engine type.
   *
   * @var string
   */
  public $engine;
  /**
   * Required. Engine version, for example "12.c.1".
   *
   * @var string
   */
  public $version;

  /**
   * Required. Engine type.
   *
   * Accepted values: DATABASE_ENGINE_UNSPECIFIED, MYSQL, POSTGRESQL, SQLSERVER,
   * ORACLE
   *
   * @param self::ENGINE_* $engine
   */
  public function setEngine($engine)
  {
    $this->engine = $engine;
  }
  /**
   * @return self::ENGINE_*
   */
  public function getEngine()
  {
    return $this->engine;
  }
  /**
   * Required. Engine version, for example "12.c.1".
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseEngineInfo::class, 'Google_Service_DatabaseMigrationService_DatabaseEngineInfo');
