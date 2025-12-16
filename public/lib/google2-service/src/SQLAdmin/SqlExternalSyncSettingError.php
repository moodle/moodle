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

namespace Google\Service\SQLAdmin;

class SqlExternalSyncSettingError extends \Google\Model
{
  public const TYPE_SQL_EXTERNAL_SYNC_SETTING_ERROR_TYPE_UNSPECIFIED = 'SQL_EXTERNAL_SYNC_SETTING_ERROR_TYPE_UNSPECIFIED';
  public const TYPE_CONNECTION_FAILURE = 'CONNECTION_FAILURE';
  public const TYPE_BINLOG_NOT_ENABLED = 'BINLOG_NOT_ENABLED';
  public const TYPE_INCOMPATIBLE_DATABASE_VERSION = 'INCOMPATIBLE_DATABASE_VERSION';
  public const TYPE_REPLICA_ALREADY_SETUP = 'REPLICA_ALREADY_SETUP';
  /**
   * The replication user is missing privileges that are required.
   */
  public const TYPE_INSUFFICIENT_PRIVILEGE = 'INSUFFICIENT_PRIVILEGE';
  /**
   * Unsupported migration type.
   */
  public const TYPE_UNSUPPORTED_MIGRATION_TYPE = 'UNSUPPORTED_MIGRATION_TYPE';
  /**
   * No pglogical extension installed on databases, applicable for postgres.
   */
  public const TYPE_NO_PGLOGICAL_INSTALLED = 'NO_PGLOGICAL_INSTALLED';
  /**
   * pglogical node already exists on databases, applicable for postgres.
   */
  public const TYPE_PGLOGICAL_NODE_ALREADY_EXISTS = 'PGLOGICAL_NODE_ALREADY_EXISTS';
  /**
   * The value of parameter wal_level is not set to logical.
   */
  public const TYPE_INVALID_WAL_LEVEL = 'INVALID_WAL_LEVEL';
  /**
   * The value of parameter shared_preload_libraries does not include pglogical.
   */
  public const TYPE_INVALID_SHARED_PRELOAD_LIBRARY = 'INVALID_SHARED_PRELOAD_LIBRARY';
  /**
   * The value of parameter max_replication_slots is not sufficient.
   */
  public const TYPE_INSUFFICIENT_MAX_REPLICATION_SLOTS = 'INSUFFICIENT_MAX_REPLICATION_SLOTS';
  /**
   * The value of parameter max_wal_senders is not sufficient.
   */
  public const TYPE_INSUFFICIENT_MAX_WAL_SENDERS = 'INSUFFICIENT_MAX_WAL_SENDERS';
  /**
   * The value of parameter max_worker_processes is not sufficient.
   */
  public const TYPE_INSUFFICIENT_MAX_WORKER_PROCESSES = 'INSUFFICIENT_MAX_WORKER_PROCESSES';
  /**
   * Extensions installed are either not supported or having unsupported
   * versions.
   */
  public const TYPE_UNSUPPORTED_EXTENSIONS = 'UNSUPPORTED_EXTENSIONS';
  /**
   * The value of parameter rds.logical_replication is not set to 1.
   */
  public const TYPE_INVALID_RDS_LOGICAL_REPLICATION = 'INVALID_RDS_LOGICAL_REPLICATION';
  /**
   * The primary instance logging setup doesn't allow EM sync.
   */
  public const TYPE_INVALID_LOGGING_SETUP = 'INVALID_LOGGING_SETUP';
  /**
   * The primary instance database parameter setup doesn't allow EM sync.
   */
  public const TYPE_INVALID_DB_PARAM = 'INVALID_DB_PARAM';
  /**
   * The gtid_mode is not supported, applicable for MySQL.
   */
  public const TYPE_UNSUPPORTED_GTID_MODE = 'UNSUPPORTED_GTID_MODE';
  /**
   * SQL Server Agent is not running.
   */
  public const TYPE_SQLSERVER_AGENT_NOT_RUNNING = 'SQLSERVER_AGENT_NOT_RUNNING';
  /**
   * The table definition is not support due to missing primary key or replica
   * identity, applicable for postgres. Note that this is a warning and won't
   * block the migration.
   */
  public const TYPE_UNSUPPORTED_TABLE_DEFINITION = 'UNSUPPORTED_TABLE_DEFINITION';
  /**
   * The customer has a definer that will break EM setup.
   */
  public const TYPE_UNSUPPORTED_DEFINER = 'UNSUPPORTED_DEFINER';
  /**
   * SQL Server @@SERVERNAME does not match actual host name.
   */
  public const TYPE_SQLSERVER_SERVERNAME_MISMATCH = 'SQLSERVER_SERVERNAME_MISMATCH';
  /**
   * The primary instance has been setup and will fail the setup.
   */
  public const TYPE_PRIMARY_ALREADY_SETUP = 'PRIMARY_ALREADY_SETUP';
  /**
   * The primary instance has unsupported binary log format.
   */
  public const TYPE_UNSUPPORTED_BINLOG_FORMAT = 'UNSUPPORTED_BINLOG_FORMAT';
  /**
   * The primary instance's binary log retention setting.
   */
  public const TYPE_BINLOG_RETENTION_SETTING = 'BINLOG_RETENTION_SETTING';
  /**
   * The primary instance has tables with unsupported storage engine.
   */
  public const TYPE_UNSUPPORTED_STORAGE_ENGINE = 'UNSUPPORTED_STORAGE_ENGINE';
  /**
   * Source has tables with limited support eg: PostgreSQL tables without
   * primary keys.
   */
  public const TYPE_LIMITED_SUPPORT_TABLES = 'LIMITED_SUPPORT_TABLES';
  /**
   * The replica instance contains existing data.
   */
  public const TYPE_EXISTING_DATA_IN_REPLICA = 'EXISTING_DATA_IN_REPLICA';
  /**
   * The replication user is missing privileges that are optional.
   */
  public const TYPE_MISSING_OPTIONAL_PRIVILEGES = 'MISSING_OPTIONAL_PRIVILEGES';
  /**
   * Additional BACKUP_ADMIN privilege is granted to the replication user which
   * may lock source MySQL 8 instance for DDLs during initial sync.
   */
  public const TYPE_RISKY_BACKUP_ADMIN_PRIVILEGE = 'RISKY_BACKUP_ADMIN_PRIVILEGE';
  /**
   * The Cloud Storage bucket is missing necessary permissions.
   */
  public const TYPE_INSUFFICIENT_GCS_PERMISSIONS = 'INSUFFICIENT_GCS_PERMISSIONS';
  /**
   * The Cloud Storage bucket has an error in the file or contains invalid file
   * information.
   */
  public const TYPE_INVALID_FILE_INFO = 'INVALID_FILE_INFO';
  /**
   * The source instance has unsupported database settings for migration.
   */
  public const TYPE_UNSUPPORTED_DATABASE_SETTINGS = 'UNSUPPORTED_DATABASE_SETTINGS';
  /**
   * The replication user is missing parallel import specific privileges. (e.g.
   * LOCK TABLES) for MySQL.
   */
  public const TYPE_MYSQL_PARALLEL_IMPORT_INSUFFICIENT_PRIVILEGE = 'MYSQL_PARALLEL_IMPORT_INSUFFICIENT_PRIVILEGE';
  /**
   * The global variable local_infile is off on external server replica.
   */
  public const TYPE_LOCAL_INFILE_OFF = 'LOCAL_INFILE_OFF';
  /**
   * This code instructs customers to turn on point-in-time recovery manually
   * for the instance after promoting the Cloud SQL for PostgreSQL instance.
   */
  public const TYPE_TURN_ON_PITR_AFTER_PROMOTE = 'TURN_ON_PITR_AFTER_PROMOTE';
  /**
   * The minor version of replica database is incompatible with the source.
   */
  public const TYPE_INCOMPATIBLE_DATABASE_MINOR_VERSION = 'INCOMPATIBLE_DATABASE_MINOR_VERSION';
  /**
   * This warning message indicates that Cloud SQL uses the maximum number of
   * subscriptions to migrate data from the source to the destination.
   */
  public const TYPE_SOURCE_MAX_SUBSCRIPTIONS = 'SOURCE_MAX_SUBSCRIPTIONS';
  /**
   * Unable to verify definers on the source for MySQL.
   */
  public const TYPE_UNABLE_TO_VERIFY_DEFINERS = 'UNABLE_TO_VERIFY_DEFINERS';
  /**
   * If a time out occurs while the subscription counts are calculated, then
   * this value is set to 1. Otherwise, this value is set to 2.
   */
  public const TYPE_SUBSCRIPTION_CALCULATION_STATUS = 'SUBSCRIPTION_CALCULATION_STATUS';
  /**
   * Count of subscriptions needed to sync source data for PostgreSQL database.
   */
  public const TYPE_PG_SUBSCRIPTION_COUNT = 'PG_SUBSCRIPTION_COUNT';
  /**
   * Final parallel level that is used to do migration.
   */
  public const TYPE_PG_SYNC_PARALLEL_LEVEL = 'PG_SYNC_PARALLEL_LEVEL';
  /**
   * The disk size of the replica instance is smaller than the data size of the
   * source instance.
   */
  public const TYPE_INSUFFICIENT_DISK_SIZE = 'INSUFFICIENT_DISK_SIZE';
  /**
   * The data size of the source instance is greater than 1 TB, the number of
   * cores of the replica instance is less than 8, and the memory of the replica
   * is less than 32 GB.
   */
  public const TYPE_INSUFFICIENT_MACHINE_TIER = 'INSUFFICIENT_MACHINE_TIER';
  /**
   * The warning message indicates the unsupported extensions will not be
   * migrated to the destination.
   */
  public const TYPE_UNSUPPORTED_EXTENSIONS_NOT_MIGRATED = 'UNSUPPORTED_EXTENSIONS_NOT_MIGRATED';
  /**
   * The warning message indicates the pg_cron extension and settings will not
   * be migrated to the destination.
   */
  public const TYPE_EXTENSIONS_NOT_MIGRATED = 'EXTENSIONS_NOT_MIGRATED';
  /**
   * The error message indicates that pg_cron flags are enabled on the
   * destination which is not supported during the migration.
   */
  public const TYPE_PG_CRON_FLAG_ENABLED_IN_REPLICA = 'PG_CRON_FLAG_ENABLED_IN_REPLICA';
  /**
   * This error message indicates that the specified extensions are not enabled
   * on destination instance. For example, before you can migrate data to the
   * destination instance, you must enable the PGAudit extension on the
   * instance.
   */
  public const TYPE_EXTENSIONS_NOT_ENABLED_IN_REPLICA = 'EXTENSIONS_NOT_ENABLED_IN_REPLICA';
  /**
   * The source database has generated columns that can't be migrated. Please
   * change them to regular columns before migration.
   */
  public const TYPE_UNSUPPORTED_COLUMNS = 'UNSUPPORTED_COLUMNS';
  /**
   * The source database has users that aren't created in the replica. First,
   * create all users, which are in the pg_user_mappings table of the source
   * database, in the destination instance. Then, perform the migration.
   */
  public const TYPE_USERS_NOT_CREATED_IN_REPLICA = 'USERS_NOT_CREATED_IN_REPLICA';
  /**
   * The selected objects include system objects that aren't supported for
   * migration.
   */
  public const TYPE_UNSUPPORTED_SYSTEM_OBJECTS = 'UNSUPPORTED_SYSTEM_OBJECTS';
  /**
   * The source database has tables with the FULL or NOTHING replica identity.
   * Before starting your migration, either remove the identity or change it to
   * DEFAULT. Note that this is an error and will block the migration.
   */
  public const TYPE_UNSUPPORTED_TABLES_WITH_REPLICA_IDENTITY = 'UNSUPPORTED_TABLES_WITH_REPLICA_IDENTITY';
  /**
   * The selected objects don't exist on the source instance.
   */
  public const TYPE_SELECTED_OBJECTS_NOT_EXIST_ON_SOURCE = 'SELECTED_OBJECTS_NOT_EXIST_ON_SOURCE';
  /**
   * PSC only destination instance does not have a network attachment URI.
   */
  public const TYPE_PSC_ONLY_INSTANCE_WITH_NO_NETWORK_ATTACHMENT_URI = 'PSC_ONLY_INSTANCE_WITH_NO_NETWORK_ATTACHMENT_URI';
  /**
   * Selected objects reference unselected objects. Based on their object type
   * (foreign key constraint or view), selected objects will fail during
   * migration.
   */
  public const TYPE_SELECTED_OBJECTS_REFERENCE_UNSELECTED_OBJECTS = 'SELECTED_OBJECTS_REFERENCE_UNSELECTED_OBJECTS';
  /**
   * The migration will delete existing data in the replica; set
   * replica_overwrite_enabled in the request to acknowledge this. This is an
   * error. MySQL only.
   */
  public const TYPE_PROMPT_DELETE_EXISTING = 'PROMPT_DELETE_EXISTING';
  /**
   * The migration will delete existing data in the replica;
   * replica_overwrite_enabled was set in the request acknowledging this. This
   * is a warning rather than an error. MySQL only.
   */
  public const TYPE_WILL_DELETE_EXISTING = 'WILL_DELETE_EXISTING';
  /**
   * The replication user is missing specific privileges to setup DDL
   * replication. (e.g. CREATE EVENT TRIGGER, CREATE SCHEMA) for PostgreSQL.
   */
  public const TYPE_PG_DDL_REPLICATION_INSUFFICIENT_PRIVILEGE = 'PG_DDL_REPLICATION_INSUFFICIENT_PRIVILEGE';
  /**
   * Additional information about the error encountered.
   *
   * @var string
   */
  public $detail;
  /**
   * Can be `sql#externalSyncSettingError` or `sql#externalSyncSettingWarning`.
   *
   * @var string
   */
  public $kind;
  /**
   * Identifies the specific error that occurred.
   *
   * @var string
   */
  public $type;

  /**
   * Additional information about the error encountered.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * Can be `sql#externalSyncSettingError` or `sql#externalSyncSettingWarning`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Identifies the specific error that occurred.
   *
   * Accepted values: SQL_EXTERNAL_SYNC_SETTING_ERROR_TYPE_UNSPECIFIED,
   * CONNECTION_FAILURE, BINLOG_NOT_ENABLED, INCOMPATIBLE_DATABASE_VERSION,
   * REPLICA_ALREADY_SETUP, INSUFFICIENT_PRIVILEGE, UNSUPPORTED_MIGRATION_TYPE,
   * NO_PGLOGICAL_INSTALLED, PGLOGICAL_NODE_ALREADY_EXISTS, INVALID_WAL_LEVEL,
   * INVALID_SHARED_PRELOAD_LIBRARY, INSUFFICIENT_MAX_REPLICATION_SLOTS,
   * INSUFFICIENT_MAX_WAL_SENDERS, INSUFFICIENT_MAX_WORKER_PROCESSES,
   * UNSUPPORTED_EXTENSIONS, INVALID_RDS_LOGICAL_REPLICATION,
   * INVALID_LOGGING_SETUP, INVALID_DB_PARAM, UNSUPPORTED_GTID_MODE,
   * SQLSERVER_AGENT_NOT_RUNNING, UNSUPPORTED_TABLE_DEFINITION,
   * UNSUPPORTED_DEFINER, SQLSERVER_SERVERNAME_MISMATCH, PRIMARY_ALREADY_SETUP,
   * UNSUPPORTED_BINLOG_FORMAT, BINLOG_RETENTION_SETTING,
   * UNSUPPORTED_STORAGE_ENGINE, LIMITED_SUPPORT_TABLES,
   * EXISTING_DATA_IN_REPLICA, MISSING_OPTIONAL_PRIVILEGES,
   * RISKY_BACKUP_ADMIN_PRIVILEGE, INSUFFICIENT_GCS_PERMISSIONS,
   * INVALID_FILE_INFO, UNSUPPORTED_DATABASE_SETTINGS,
   * MYSQL_PARALLEL_IMPORT_INSUFFICIENT_PRIVILEGE, LOCAL_INFILE_OFF,
   * TURN_ON_PITR_AFTER_PROMOTE, INCOMPATIBLE_DATABASE_MINOR_VERSION,
   * SOURCE_MAX_SUBSCRIPTIONS, UNABLE_TO_VERIFY_DEFINERS,
   * SUBSCRIPTION_CALCULATION_STATUS, PG_SUBSCRIPTION_COUNT,
   * PG_SYNC_PARALLEL_LEVEL, INSUFFICIENT_DISK_SIZE, INSUFFICIENT_MACHINE_TIER,
   * UNSUPPORTED_EXTENSIONS_NOT_MIGRATED, EXTENSIONS_NOT_MIGRATED,
   * PG_CRON_FLAG_ENABLED_IN_REPLICA, EXTENSIONS_NOT_ENABLED_IN_REPLICA,
   * UNSUPPORTED_COLUMNS, USERS_NOT_CREATED_IN_REPLICA,
   * UNSUPPORTED_SYSTEM_OBJECTS, UNSUPPORTED_TABLES_WITH_REPLICA_IDENTITY,
   * SELECTED_OBJECTS_NOT_EXIST_ON_SOURCE,
   * PSC_ONLY_INSTANCE_WITH_NO_NETWORK_ATTACHMENT_URI,
   * SELECTED_OBJECTS_REFERENCE_UNSELECTED_OBJECTS, PROMPT_DELETE_EXISTING,
   * WILL_DELETE_EXISTING, PG_DDL_REPLICATION_INSUFFICIENT_PRIVILEGE
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
class_alias(SqlExternalSyncSettingError::class, 'Google_Service_SQLAdmin_SqlExternalSyncSettingError');
