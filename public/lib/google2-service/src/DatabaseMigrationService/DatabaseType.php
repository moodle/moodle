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

class DatabaseType extends \Google\Model
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
   * Use this value for on-premise source database instances and ORACLE.
   */
  public const PROVIDER_DATABASE_PROVIDER_UNSPECIFIED = 'DATABASE_PROVIDER_UNSPECIFIED';
  /**
   * Cloud SQL is the source instance provider.
   */
  public const PROVIDER_CLOUDSQL = 'CLOUDSQL';
  /**
   * Amazon RDS is the source instance provider.
   */
  public const PROVIDER_RDS = 'RDS';
  /**
   * Amazon Aurora is the source instance provider.
   */
  public const PROVIDER_AURORA = 'AURORA';
  /**
   * AlloyDB for PostgreSQL is the source instance provider.
   */
  public const PROVIDER_ALLOYDB = 'ALLOYDB';
  /**
   * Microsoft Azure Database for MySQL/PostgreSQL.
   */
  public const PROVIDER_AZURE_DATABASE = 'AZURE_DATABASE';
  /**
   * The database engine.
   *
   * @var string
   */
  public $engine;
  /**
   * The database provider.
   *
   * @var string
   */
  public $provider;

  /**
   * The database engine.
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
   * The database provider.
   *
   * Accepted values: DATABASE_PROVIDER_UNSPECIFIED, CLOUDSQL, RDS, AURORA,
   * ALLOYDB, AZURE_DATABASE
   *
   * @param self::PROVIDER_* $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return self::PROVIDER_*
   */
  public function getProvider()
  {
    return $this->provider;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseType::class, 'Google_Service_DatabaseMigrationService_DatabaseType');
