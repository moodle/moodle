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

namespace Google\Service\FirebaseDataConnect;

class PostgreSql extends \Google\Model
{
  /**
   * Unspecified SQL schema migration.
   */
  public const SCHEMA_MIGRATION_SQL_SCHEMA_MIGRATION_UNSPECIFIED = 'SQL_SCHEMA_MIGRATION_UNSPECIFIED';
  /**
   * Connect to the SQL database and identify any missing SQL resources used in
   * the given Firebase Data Connect Schema. Automatically create necessary SQL
   * resources (SQL table, column, etc) before deploying the schema. During
   * migration steps, the SQL Schema must comply with the previous before_deploy
   * setting in case the migration is interrupted. Therefore, the previous
   * before_deploy setting must not be `schema_validation=STRICT`.
   */
  public const SCHEMA_MIGRATION_MIGRATE_COMPATIBLE = 'MIGRATE_COMPATIBLE';
  /**
   * Unspecified SQL schema validation. Default to STRICT.
   */
  public const SCHEMA_VALIDATION_SQL_SCHEMA_VALIDATION_UNSPECIFIED = 'SQL_SCHEMA_VALIDATION_UNSPECIFIED';
  /**
   * Skip no SQL schema validation. Use it with extreme caution. CreateSchema or
   * UpdateSchema will succeed even if SQL database is unavailable or SQL schema
   * is incompatible. Generated SQL may fail at execution time.
   */
  public const SCHEMA_VALIDATION_NONE = 'NONE';
  /**
   * Connect to the SQL database and validate that the SQL DDL matches the
   * schema exactly. Surface any discrepancies as `FAILED_PRECONDITION` with an
   * `IncompatibleSqlSchemaError` error detail.
   */
  public const SCHEMA_VALIDATION_STRICT = 'STRICT';
  /**
   * Connect to the SQL database and validate that the SQL DDL has all the SQL
   * resources used in the given Firebase Data Connect Schema. Surface any
   * missing resources as `FAILED_PRECONDITION` with an
   * `IncompatibleSqlSchemaError` error detail. Succeed even if there are
   * unknown tables and columns.
   */
  public const SCHEMA_VALIDATION_COMPATIBLE = 'COMPATIBLE';
  protected $cloudSqlType = CloudSqlInstance::class;
  protected $cloudSqlDataType = '';
  /**
   * Required. Name of the PostgreSQL database.
   *
   * @var string
   */
  public $database;
  /**
   * Output only. Ephemeral is true if this data connect service is served from
   * temporary in-memory emulation of Postgres. While Cloud SQL is being
   * provisioned, the data connect service provides the ephemeral service to
   * help developers get started. Once the Cloud SQL is provisioned, Data
   * Connect service will transfer its data on a best-effort basis to the Cloud
   * SQL instance. WARNING: Ephemeral data sources will expire after 24 hour.
   * The data will be lost if they aren't transferred to the Cloud SQL instance.
   * WARNING: When `ephemeral=true`, mutations to the database are not
   * guaranteed to be durably persisted, even if an OK status code is returned.
   * All or parts of the data may be lost or reverted to earlier versions.
   *
   * @var bool
   */
  public $ephemeral;
  /**
   * Optional. Configure how to perform Postgresql schema migration.
   *
   * @var string
   */
  public $schemaMigration;
  /**
   * Optional. Configure how much Postgresql schema validation to perform.
   *
   * @var string
   */
  public $schemaValidation;
  /**
   * No Postgres data source is linked. If set, don't allow `database` and
   * `schema_validation` to be configured.
   *
   * @deprecated
   * @var bool
   */
  public $unlinked;

  /**
   * Cloud SQL configurations.
   *
   * @param CloudSqlInstance $cloudSql
   */
  public function setCloudSql(CloudSqlInstance $cloudSql)
  {
    $this->cloudSql = $cloudSql;
  }
  /**
   * @return CloudSqlInstance
   */
  public function getCloudSql()
  {
    return $this->cloudSql;
  }
  /**
   * Required. Name of the PostgreSQL database.
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
   * Output only. Ephemeral is true if this data connect service is served from
   * temporary in-memory emulation of Postgres. While Cloud SQL is being
   * provisioned, the data connect service provides the ephemeral service to
   * help developers get started. Once the Cloud SQL is provisioned, Data
   * Connect service will transfer its data on a best-effort basis to the Cloud
   * SQL instance. WARNING: Ephemeral data sources will expire after 24 hour.
   * The data will be lost if they aren't transferred to the Cloud SQL instance.
   * WARNING: When `ephemeral=true`, mutations to the database are not
   * guaranteed to be durably persisted, even if an OK status code is returned.
   * All or parts of the data may be lost or reverted to earlier versions.
   *
   * @param bool $ephemeral
   */
  public function setEphemeral($ephemeral)
  {
    $this->ephemeral = $ephemeral;
  }
  /**
   * @return bool
   */
  public function getEphemeral()
  {
    return $this->ephemeral;
  }
  /**
   * Optional. Configure how to perform Postgresql schema migration.
   *
   * Accepted values: SQL_SCHEMA_MIGRATION_UNSPECIFIED, MIGRATE_COMPATIBLE
   *
   * @param self::SCHEMA_MIGRATION_* $schemaMigration
   */
  public function setSchemaMigration($schemaMigration)
  {
    $this->schemaMigration = $schemaMigration;
  }
  /**
   * @return self::SCHEMA_MIGRATION_*
   */
  public function getSchemaMigration()
  {
    return $this->schemaMigration;
  }
  /**
   * Optional. Configure how much Postgresql schema validation to perform.
   *
   * Accepted values: SQL_SCHEMA_VALIDATION_UNSPECIFIED, NONE, STRICT,
   * COMPATIBLE
   *
   * @param self::SCHEMA_VALIDATION_* $schemaValidation
   */
  public function setSchemaValidation($schemaValidation)
  {
    $this->schemaValidation = $schemaValidation;
  }
  /**
   * @return self::SCHEMA_VALIDATION_*
   */
  public function getSchemaValidation()
  {
    return $this->schemaValidation;
  }
  /**
   * No Postgres data source is linked. If set, don't allow `database` and
   * `schema_validation` to be configured.
   *
   * @deprecated
   * @param bool $unlinked
   */
  public function setUnlinked($unlinked)
  {
    $this->unlinked = $unlinked;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getUnlinked()
  {
    return $this->unlinked;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostgreSql::class, 'Google_Service_FirebaseDataConnect_PostgreSql');
