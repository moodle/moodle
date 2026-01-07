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

class MigrationJobVerificationError extends \Google\Model
{
  /**
   * An unknown error occurred
   */
  public const ERROR_CODE_ERROR_CODE_UNSPECIFIED = 'ERROR_CODE_UNSPECIFIED';
  /**
   * We failed to connect to one of the connection profile.
   */
  public const ERROR_CODE_CONNECTION_FAILURE = 'CONNECTION_FAILURE';
  /**
   * We failed to authenticate to one of the connection profile.
   */
  public const ERROR_CODE_AUTHENTICATION_FAILURE = 'AUTHENTICATION_FAILURE';
  /**
   * One of the involved connection profiles has an invalid configuration.
   */
  public const ERROR_CODE_INVALID_CONNECTION_PROFILE_CONFIG = 'INVALID_CONNECTION_PROFILE_CONFIG';
  /**
   * The versions of the source and the destination are incompatible.
   */
  public const ERROR_CODE_VERSION_INCOMPATIBILITY = 'VERSION_INCOMPATIBILITY';
  /**
   * The types of the source and the destination are incompatible.
   */
  public const ERROR_CODE_CONNECTION_PROFILE_TYPES_INCOMPATIBILITY = 'CONNECTION_PROFILE_TYPES_INCOMPATIBILITY';
  /**
   * No pglogical extension installed on databases, applicable for postgres.
   */
  public const ERROR_CODE_NO_PGLOGICAL_INSTALLED = 'NO_PGLOGICAL_INSTALLED';
  /**
   * pglogical node already exists on databases, applicable for postgres.
   */
  public const ERROR_CODE_PGLOGICAL_NODE_ALREADY_EXISTS = 'PGLOGICAL_NODE_ALREADY_EXISTS';
  /**
   * The value of parameter wal_level is not set to logical.
   */
  public const ERROR_CODE_INVALID_WAL_LEVEL = 'INVALID_WAL_LEVEL';
  /**
   * The value of parameter shared_preload_libraries does not include pglogical.
   */
  public const ERROR_CODE_INVALID_SHARED_PRELOAD_LIBRARY = 'INVALID_SHARED_PRELOAD_LIBRARY';
  /**
   * The value of parameter max_replication_slots is not sufficient.
   */
  public const ERROR_CODE_INSUFFICIENT_MAX_REPLICATION_SLOTS = 'INSUFFICIENT_MAX_REPLICATION_SLOTS';
  /**
   * The value of parameter max_wal_senders is not sufficient.
   */
  public const ERROR_CODE_INSUFFICIENT_MAX_WAL_SENDERS = 'INSUFFICIENT_MAX_WAL_SENDERS';
  /**
   * The value of parameter max_worker_processes is not sufficient.
   */
  public const ERROR_CODE_INSUFFICIENT_MAX_WORKER_PROCESSES = 'INSUFFICIENT_MAX_WORKER_PROCESSES';
  /**
   * Extensions installed are either not supported or having unsupported
   * versions.
   */
  public const ERROR_CODE_UNSUPPORTED_EXTENSIONS = 'UNSUPPORTED_EXTENSIONS';
  /**
   * Unsupported migration type.
   */
  public const ERROR_CODE_UNSUPPORTED_MIGRATION_TYPE = 'UNSUPPORTED_MIGRATION_TYPE';
  /**
   * Invalid RDS logical replication.
   */
  public const ERROR_CODE_INVALID_RDS_LOGICAL_REPLICATION = 'INVALID_RDS_LOGICAL_REPLICATION';
  /**
   * The gtid_mode is not supported, applicable for MySQL.
   */
  public const ERROR_CODE_UNSUPPORTED_GTID_MODE = 'UNSUPPORTED_GTID_MODE';
  /**
   * The table definition is not support due to missing primary key or replica
   * identity.
   */
  public const ERROR_CODE_UNSUPPORTED_TABLE_DEFINITION = 'UNSUPPORTED_TABLE_DEFINITION';
  /**
   * The definer is not supported.
   */
  public const ERROR_CODE_UNSUPPORTED_DEFINER = 'UNSUPPORTED_DEFINER';
  /**
   * Migration is already running at the time of restart request.
   */
  public const ERROR_CODE_CANT_RESTART_RUNNING_MIGRATION = 'CANT_RESTART_RUNNING_MIGRATION';
  /**
   * The source already has a replication setup.
   */
  public const ERROR_CODE_SOURCE_ALREADY_SETUP = 'SOURCE_ALREADY_SETUP';
  /**
   * The source has tables with limited support. E.g. PostgreSQL tables without
   * primary keys.
   */
  public const ERROR_CODE_TABLES_WITH_LIMITED_SUPPORT = 'TABLES_WITH_LIMITED_SUPPORT';
  /**
   * The source uses an unsupported locale.
   */
  public const ERROR_CODE_UNSUPPORTED_DATABASE_LOCALE = 'UNSUPPORTED_DATABASE_LOCALE';
  /**
   * The source uses an unsupported Foreign Data Wrapper configuration.
   */
  public const ERROR_CODE_UNSUPPORTED_DATABASE_FDW_CONFIG = 'UNSUPPORTED_DATABASE_FDW_CONFIG';
  /**
   * There was an underlying RDBMS error.
   */
  public const ERROR_CODE_ERROR_RDBMS = 'ERROR_RDBMS';
  /**
   * The source DB size in Bytes exceeds a certain threshold. The migration
   * might require an increase of quota, or might not be supported.
   */
  public const ERROR_CODE_SOURCE_SIZE_EXCEEDS_THRESHOLD = 'SOURCE_SIZE_EXCEEDS_THRESHOLD';
  /**
   * The destination DB contains existing databases that are conflicting with
   * those in the source DB.
   */
  public const ERROR_CODE_EXISTING_CONFLICTING_DATABASES = 'EXISTING_CONFLICTING_DATABASES';
  /**
   * Insufficient privilege to enable the parallelism configuration.
   */
  public const ERROR_CODE_PARALLEL_IMPORT_INSUFFICIENT_PRIVILEGE = 'PARALLEL_IMPORT_INSUFFICIENT_PRIVILEGE';
  /**
   * The destination instance contains existing data or user defined entities
   * (for example databases, tables, or functions). You can only migrate to
   * empty instances. Clear your destination instance and retry the migration
   * job.
   */
  public const ERROR_CODE_EXISTING_DATA = 'EXISTING_DATA';
  /**
   * The migration job is configured to use max number of subscriptions to
   * migrate data from the source to the destination.
   */
  public const ERROR_CODE_SOURCE_MAX_SUBSCRIPTIONS = 'SOURCE_MAX_SUBSCRIPTIONS';
  /**
   * Output only. An instance of ErrorCode specifying the error that occurred.
   *
   * @var string
   */
  public $errorCode;
  /**
   * Output only. A specific detailed error message, if supplied by the engine.
   *
   * @var string
   */
  public $errorDetailMessage;
  /**
   * Output only. A formatted message with further details about the error and a
   * CTA.
   *
   * @var string
   */
  public $errorMessage;

  /**
   * Output only. An instance of ErrorCode specifying the error that occurred.
   *
   * Accepted values: ERROR_CODE_UNSPECIFIED, CONNECTION_FAILURE,
   * AUTHENTICATION_FAILURE, INVALID_CONNECTION_PROFILE_CONFIG,
   * VERSION_INCOMPATIBILITY, CONNECTION_PROFILE_TYPES_INCOMPATIBILITY,
   * NO_PGLOGICAL_INSTALLED, PGLOGICAL_NODE_ALREADY_EXISTS, INVALID_WAL_LEVEL,
   * INVALID_SHARED_PRELOAD_LIBRARY, INSUFFICIENT_MAX_REPLICATION_SLOTS,
   * INSUFFICIENT_MAX_WAL_SENDERS, INSUFFICIENT_MAX_WORKER_PROCESSES,
   * UNSUPPORTED_EXTENSIONS, UNSUPPORTED_MIGRATION_TYPE,
   * INVALID_RDS_LOGICAL_REPLICATION, UNSUPPORTED_GTID_MODE,
   * UNSUPPORTED_TABLE_DEFINITION, UNSUPPORTED_DEFINER,
   * CANT_RESTART_RUNNING_MIGRATION, SOURCE_ALREADY_SETUP,
   * TABLES_WITH_LIMITED_SUPPORT, UNSUPPORTED_DATABASE_LOCALE,
   * UNSUPPORTED_DATABASE_FDW_CONFIG, ERROR_RDBMS,
   * SOURCE_SIZE_EXCEEDS_THRESHOLD, EXISTING_CONFLICTING_DATABASES,
   * PARALLEL_IMPORT_INSUFFICIENT_PRIVILEGE, EXISTING_DATA,
   * SOURCE_MAX_SUBSCRIPTIONS
   *
   * @param self::ERROR_CODE_* $errorCode
   */
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return self::ERROR_CODE_*
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * Output only. A specific detailed error message, if supplied by the engine.
   *
   * @param string $errorDetailMessage
   */
  public function setErrorDetailMessage($errorDetailMessage)
  {
    $this->errorDetailMessage = $errorDetailMessage;
  }
  /**
   * @return string
   */
  public function getErrorDetailMessage()
  {
    return $this->errorDetailMessage;
  }
  /**
   * Output only. A formatted message with further details about the error and a
   * CTA.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MigrationJobVerificationError::class, 'Google_Service_DatabaseMigrationService_MigrationJobVerificationError');
