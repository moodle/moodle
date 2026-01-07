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

class SqlInstancesStartExternalSyncRequest extends \Google\Model
{
  /**
   * Default value is a logical dump file-based migration
   */
  public const MIGRATION_TYPE_MIGRATION_TYPE_UNSPECIFIED = 'MIGRATION_TYPE_UNSPECIFIED';
  /**
   * Logical dump file-based migration
   */
  public const MIGRATION_TYPE_LOGICAL = 'LOGICAL';
  /**
   * Physical file-based migration
   */
  public const MIGRATION_TYPE_PHYSICAL = 'PHYSICAL';
  /**
   * Unknown external sync mode, will be defaulted to ONLINE mode
   */
  public const SYNC_MODE_EXTERNAL_SYNC_MODE_UNSPECIFIED = 'EXTERNAL_SYNC_MODE_UNSPECIFIED';
  /**
   * Online external sync will set up replication after initial data external
   * sync
   */
  public const SYNC_MODE_ONLINE = 'ONLINE';
  /**
   * Offline external sync only dumps and loads a one-time snapshot of the
   * primary instance's data
   */
  public const SYNC_MODE_OFFLINE = 'OFFLINE';
  /**
   * Unknown sync parallel level. Will be defaulted to OPTIMAL.
   */
  public const SYNC_PARALLEL_LEVEL_EXTERNAL_SYNC_PARALLEL_LEVEL_UNSPECIFIED = 'EXTERNAL_SYNC_PARALLEL_LEVEL_UNSPECIFIED';
  /**
   * Minimal parallel level.
   */
  public const SYNC_PARALLEL_LEVEL_MIN = 'MIN';
  /**
   * Optimal parallel level.
   */
  public const SYNC_PARALLEL_LEVEL_OPTIMAL = 'OPTIMAL';
  /**
   * Maximum parallel level.
   */
  public const SYNC_PARALLEL_LEVEL_MAX = 'MAX';
  /**
   * Optional. MigrationType configures the migration to use physical files or
   * logical dump files. If not set, then the logical dump file configuration is
   * used. Valid values are `LOGICAL` or `PHYSICAL`. Only applicable to MySQL.
   *
   * @var string
   */
  public $migrationType;
  protected $mysqlSyncConfigType = MySqlSyncConfig::class;
  protected $mysqlSyncConfigDataType = '';
  /**
   * Optional. MySQL only. True if end-user has confirmed that this SES call
   * will wipe replica databases overlapping with the proposed selected_objects.
   * If this field is not set and there are both overlapping and additional
   * databases proposed, an error will be returned.
   *
   * @var bool
   */
  public $replicaOverwriteEnabled;
  /**
   * Whether to skip the verification step (VESS).
   *
   * @var bool
   */
  public $skipVerification;
  /**
   * External sync mode.
   *
   * @var string
   */
  public $syncMode;
  /**
   * Optional. Parallel level for initial data sync. Currently only applicable
   * for MySQL.
   *
   * @var string
   */
  public $syncParallelLevel;

  /**
   * Optional. MigrationType configures the migration to use physical files or
   * logical dump files. If not set, then the logical dump file configuration is
   * used. Valid values are `LOGICAL` or `PHYSICAL`. Only applicable to MySQL.
   *
   * Accepted values: MIGRATION_TYPE_UNSPECIFIED, LOGICAL, PHYSICAL
   *
   * @param self::MIGRATION_TYPE_* $migrationType
   */
  public function setMigrationType($migrationType)
  {
    $this->migrationType = $migrationType;
  }
  /**
   * @return self::MIGRATION_TYPE_*
   */
  public function getMigrationType()
  {
    return $this->migrationType;
  }
  /**
   * MySQL-specific settings for start external sync.
   *
   * @param MySqlSyncConfig $mysqlSyncConfig
   */
  public function setMysqlSyncConfig(MySqlSyncConfig $mysqlSyncConfig)
  {
    $this->mysqlSyncConfig = $mysqlSyncConfig;
  }
  /**
   * @return MySqlSyncConfig
   */
  public function getMysqlSyncConfig()
  {
    return $this->mysqlSyncConfig;
  }
  /**
   * Optional. MySQL only. True if end-user has confirmed that this SES call
   * will wipe replica databases overlapping with the proposed selected_objects.
   * If this field is not set and there are both overlapping and additional
   * databases proposed, an error will be returned.
   *
   * @param bool $replicaOverwriteEnabled
   */
  public function setReplicaOverwriteEnabled($replicaOverwriteEnabled)
  {
    $this->replicaOverwriteEnabled = $replicaOverwriteEnabled;
  }
  /**
   * @return bool
   */
  public function getReplicaOverwriteEnabled()
  {
    return $this->replicaOverwriteEnabled;
  }
  /**
   * Whether to skip the verification step (VESS).
   *
   * @param bool $skipVerification
   */
  public function setSkipVerification($skipVerification)
  {
    $this->skipVerification = $skipVerification;
  }
  /**
   * @return bool
   */
  public function getSkipVerification()
  {
    return $this->skipVerification;
  }
  /**
   * External sync mode.
   *
   * Accepted values: EXTERNAL_SYNC_MODE_UNSPECIFIED, ONLINE, OFFLINE
   *
   * @param self::SYNC_MODE_* $syncMode
   */
  public function setSyncMode($syncMode)
  {
    $this->syncMode = $syncMode;
  }
  /**
   * @return self::SYNC_MODE_*
   */
  public function getSyncMode()
  {
    return $this->syncMode;
  }
  /**
   * Optional. Parallel level for initial data sync. Currently only applicable
   * for MySQL.
   *
   * Accepted values: EXTERNAL_SYNC_PARALLEL_LEVEL_UNSPECIFIED, MIN, OPTIMAL,
   * MAX
   *
   * @param self::SYNC_PARALLEL_LEVEL_* $syncParallelLevel
   */
  public function setSyncParallelLevel($syncParallelLevel)
  {
    $this->syncParallelLevel = $syncParallelLevel;
  }
  /**
   * @return self::SYNC_PARALLEL_LEVEL_*
   */
  public function getSyncParallelLevel()
  {
    return $this->syncParallelLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlInstancesStartExternalSyncRequest::class, 'Google_Service_SQLAdmin_SqlInstancesStartExternalSyncRequest');
