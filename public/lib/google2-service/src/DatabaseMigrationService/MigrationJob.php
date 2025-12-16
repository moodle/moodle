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

class MigrationJob extends \Google\Model
{
  /**
   * If not specified, defaults to LOGICAL
   */
  public const DUMP_TYPE_DUMP_TYPE_UNSPECIFIED = 'DUMP_TYPE_UNSPECIFIED';
  /**
   * Logical dump.
   */
  public const DUMP_TYPE_LOGICAL = 'LOGICAL';
  /**
   * Physical file-based dump. Supported for MySQL to CloudSQL for MySQL
   * migrations only.
   */
  public const DUMP_TYPE_PHYSICAL = 'PHYSICAL';
  /**
   * The phase of the migration job is unknown.
   */
  public const PHASE_PHASE_UNSPECIFIED = 'PHASE_UNSPECIFIED';
  /**
   * The migration job is in the full dump phase.
   */
  public const PHASE_FULL_DUMP = 'FULL_DUMP';
  /**
   * The migration job is CDC phase.
   */
  public const PHASE_CDC = 'CDC';
  /**
   * The migration job is running the promote phase.
   */
  public const PHASE_PROMOTE_IN_PROGRESS = 'PROMOTE_IN_PROGRESS';
  /**
   * Only RDS flow - waiting for source writes to stop
   */
  public const PHASE_WAITING_FOR_SOURCE_WRITES_TO_STOP = 'WAITING_FOR_SOURCE_WRITES_TO_STOP';
  /**
   * Only RDS flow - the sources writes stopped, waiting for dump to begin
   */
  public const PHASE_PREPARING_THE_DUMP = 'PREPARING_THE_DUMP';
  /**
   * The migration job is ready to be promoted.
   */
  public const PHASE_READY_FOR_PROMOTE = 'READY_FOR_PROMOTE';
  /**
   * The state of the migration job is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The migration job is down for maintenance.
   */
  public const STATE_MAINTENANCE = 'MAINTENANCE';
  /**
   * The migration job is in draft mode and no resources are created.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * The migration job is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The migration job is created and not started.
   */
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  /**
   * The migration job is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The migration job failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The migration job has been completed.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * The migration job is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The migration job is being stopped.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * The migration job is currently stopped.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * The migration job has been deleted.
   */
  public const STATE_DELETED = 'DELETED';
  /**
   * The migration job is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The migration job is starting.
   */
  public const STATE_STARTING = 'STARTING';
  /**
   * The migration job is restarting.
   */
  public const STATE_RESTARTING = 'RESTARTING';
  /**
   * The migration job is resuming.
   */
  public const STATE_RESUMING = 'RESUMING';
  /**
   * The type of the migration job is unknown.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The migration job is a one time migration.
   */
  public const TYPE_ONE_TIME = 'ONE_TIME';
  /**
   * The migration job is a continuous migration.
   */
  public const TYPE_CONTINUOUS = 'CONTINUOUS';
  /**
   * The CMEK (customer-managed encryption key) fully qualified key name used
   * for the migration job. This field supports all migration jobs types except
   * for: * Mysql to Mysql (use the cmek field in the cloudsql connection
   * profile instead). * PostrgeSQL to PostgreSQL (use the cmek field in the
   * cloudsql connection profile instead). * PostgreSQL to AlloyDB (use the
   * kms_key_name field in the alloydb connection profile instead). Each Cloud
   * CMEK key has the following format:
   * projects/[PROJECT]/locations/[REGION]/keyRings/[RING]/cryptoKeys/[KEY_NAME]
   *
   * @var string
   */
  public $cmekKeyName;
  protected $conversionWorkspaceType = ConversionWorkspaceInfo::class;
  protected $conversionWorkspaceDataType = '';
  /**
   * Output only. The timestamp when the migration job resource was created. A
   * timestamp in RFC3339 UTC "Zulu" format, accurate to nanoseconds. Example:
   * "2014-10-02T15:01:23.045123456Z".
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The resource name (URI) of the destination connection profile.
   *
   * @var string
   */
  public $destination;
  protected $destinationDatabaseType = DatabaseType::class;
  protected $destinationDatabaseDataType = '';
  /**
   * The migration job display name.
   *
   * @var string
   */
  public $displayName;
  protected $dumpFlagsType = DumpFlags::class;
  protected $dumpFlagsDataType = '';
  /**
   * The path to the dump file in Google Cloud Storage, in the format:
   * (gs://[BUCKET_NAME]/[OBJECT_NAME]). This field and the "dump_flags" field
   * are mutually exclusive.
   *
   * @var string
   */
  public $dumpPath;
  /**
   * Optional. The type of the data dump. Supported for MySQL to CloudSQL for
   * MySQL migrations only.
   *
   * @var string
   */
  public $dumpType;
  /**
   * Output only. The duration of the migration job (in seconds). A duration in
   * seconds with up to nine fractional digits, terminated by 's'. Example:
   * "3.5s".
   *
   * @var string
   */
  public $duration;
  /**
   * Output only. If the migration job is completed, the time when it was
   * completed.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * This field can be used to select the entities to migrate as part of the
   * migration job. It uses AIP-160 notation to select a subset of the entities
   * configured on the associated conversion-workspace. This field should not be
   * set on migration-jobs that are not associated with a conversion workspace.
   *
   * @var string
   */
  public $filter;
  /**
   * The resource labels for migration job to use to annotate any related
   * underlying resources such as Compute Engine VMs. An object containing a
   * list of "key": "value" pairs. Example: `{ "name": "wrench", "mass":
   * "1.3kg", "count": "3" }`.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The name (URI) of this migration job resource, in the form of:
   * projects/{project}/locations/{location}/migrationJobs/{migrationJob}.
   *
   * @var string
   */
  public $name;
  protected $objectsConfigType = MigrationJobObjectsConfig::class;
  protected $objectsConfigDataType = '';
  protected $oracleToPostgresConfigType = OracleToPostgresConfig::class;
  protected $oracleToPostgresConfigDataType = '';
  protected $performanceConfigType = PerformanceConfig::class;
  protected $performanceConfigDataType = '';
  /**
   * Output only. The current migration job phase.
   *
   * @var string
   */
  public $phase;
  protected $reverseSshConnectivityType = ReverseSshConnectivity::class;
  protected $reverseSshConnectivityDataType = '';
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Required. The resource name (URI) of the source connection profile.
   *
   * @var string
   */
  public $source;
  protected $sourceDatabaseType = DatabaseType::class;
  protected $sourceDatabaseDataType = '';
  protected $sqlserverHomogeneousMigrationJobConfigType = SqlServerHomogeneousMigrationJobConfig::class;
  protected $sqlserverHomogeneousMigrationJobConfigDataType = '';
  protected $sqlserverToPostgresConfigType = SqlServerToPostgresConfig::class;
  protected $sqlserverToPostgresConfigDataType = '';
  /**
   * The current migration job state.
   *
   * @var string
   */
  public $state;
  protected $staticIpConnectivityType = StaticIpConnectivity::class;
  protected $staticIpConnectivityDataType = '';
  /**
   * Required. The migration job type.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The timestamp when the migration job resource was last
   * updated. A timestamp in RFC3339 UTC "Zulu" format, accurate to nanoseconds.
   * Example: "2014-10-02T15:01:23.045123456Z".
   *
   * @var string
   */
  public $updateTime;
  protected $vpcPeeringConnectivityType = VpcPeeringConnectivity::class;
  protected $vpcPeeringConnectivityDataType = '';

  /**
   * The CMEK (customer-managed encryption key) fully qualified key name used
   * for the migration job. This field supports all migration jobs types except
   * for: * Mysql to Mysql (use the cmek field in the cloudsql connection
   * profile instead). * PostrgeSQL to PostgreSQL (use the cmek field in the
   * cloudsql connection profile instead). * PostgreSQL to AlloyDB (use the
   * kms_key_name field in the alloydb connection profile instead). Each Cloud
   * CMEK key has the following format:
   * projects/[PROJECT]/locations/[REGION]/keyRings/[RING]/cryptoKeys/[KEY_NAME]
   *
   * @param string $cmekKeyName
   */
  public function setCmekKeyName($cmekKeyName)
  {
    $this->cmekKeyName = $cmekKeyName;
  }
  /**
   * @return string
   */
  public function getCmekKeyName()
  {
    return $this->cmekKeyName;
  }
  /**
   * The conversion workspace used by the migration.
   *
   * @param ConversionWorkspaceInfo $conversionWorkspace
   */
  public function setConversionWorkspace(ConversionWorkspaceInfo $conversionWorkspace)
  {
    $this->conversionWorkspace = $conversionWorkspace;
  }
  /**
   * @return ConversionWorkspaceInfo
   */
  public function getConversionWorkspace()
  {
    return $this->conversionWorkspace;
  }
  /**
   * Output only. The timestamp when the migration job resource was created. A
   * timestamp in RFC3339 UTC "Zulu" format, accurate to nanoseconds. Example:
   * "2014-10-02T15:01:23.045123456Z".
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
   * Required. The resource name (URI) of the destination connection profile.
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * The database engine type and provider of the destination.
   *
   * @param DatabaseType $destinationDatabase
   */
  public function setDestinationDatabase(DatabaseType $destinationDatabase)
  {
    $this->destinationDatabase = $destinationDatabase;
  }
  /**
   * @return DatabaseType
   */
  public function getDestinationDatabase()
  {
    return $this->destinationDatabase;
  }
  /**
   * The migration job display name.
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
   * The initial dump flags. This field and the "dump_path" field are mutually
   * exclusive.
   *
   * @param DumpFlags $dumpFlags
   */
  public function setDumpFlags(DumpFlags $dumpFlags)
  {
    $this->dumpFlags = $dumpFlags;
  }
  /**
   * @return DumpFlags
   */
  public function getDumpFlags()
  {
    return $this->dumpFlags;
  }
  /**
   * The path to the dump file in Google Cloud Storage, in the format:
   * (gs://[BUCKET_NAME]/[OBJECT_NAME]). This field and the "dump_flags" field
   * are mutually exclusive.
   *
   * @param string $dumpPath
   */
  public function setDumpPath($dumpPath)
  {
    $this->dumpPath = $dumpPath;
  }
  /**
   * @return string
   */
  public function getDumpPath()
  {
    return $this->dumpPath;
  }
  /**
   * Optional. The type of the data dump. Supported for MySQL to CloudSQL for
   * MySQL migrations only.
   *
   * Accepted values: DUMP_TYPE_UNSPECIFIED, LOGICAL, PHYSICAL
   *
   * @param self::DUMP_TYPE_* $dumpType
   */
  public function setDumpType($dumpType)
  {
    $this->dumpType = $dumpType;
  }
  /**
   * @return self::DUMP_TYPE_*
   */
  public function getDumpType()
  {
    return $this->dumpType;
  }
  /**
   * Output only. The duration of the migration job (in seconds). A duration in
   * seconds with up to nine fractional digits, terminated by 's'. Example:
   * "3.5s".
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Output only. If the migration job is completed, the time when it was
   * completed.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. The error details in case of state FAILED.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * This field can be used to select the entities to migrate as part of the
   * migration job. It uses AIP-160 notation to select a subset of the entities
   * configured on the associated conversion-workspace. This field should not be
   * set on migration-jobs that are not associated with a conversion workspace.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * The resource labels for migration job to use to annotate any related
   * underlying resources such as Compute Engine VMs. An object containing a
   * list of "key": "value" pairs. Example: `{ "name": "wrench", "mass":
   * "1.3kg", "count": "3" }`.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The name (URI) of this migration job resource, in the form of:
   * projects/{project}/locations/{location}/migrationJobs/{migrationJob}.
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
   * Optional. The objects that need to be migrated.
   *
   * @param MigrationJobObjectsConfig $objectsConfig
   */
  public function setObjectsConfig(MigrationJobObjectsConfig $objectsConfig)
  {
    $this->objectsConfig = $objectsConfig;
  }
  /**
   * @return MigrationJobObjectsConfig
   */
  public function getObjectsConfig()
  {
    return $this->objectsConfig;
  }
  /**
   * Configuration for heterogeneous **Oracle to Cloud SQL for PostgreSQL** and
   * **Oracle to AlloyDB for PostgreSQL** migrations.
   *
   * @param OracleToPostgresConfig $oracleToPostgresConfig
   */
  public function setOracleToPostgresConfig(OracleToPostgresConfig $oracleToPostgresConfig)
  {
    $this->oracleToPostgresConfig = $oracleToPostgresConfig;
  }
  /**
   * @return OracleToPostgresConfig
   */
  public function getOracleToPostgresConfig()
  {
    return $this->oracleToPostgresConfig;
  }
  /**
   * Optional. Data dump parallelism settings used by the migration.
   *
   * @param PerformanceConfig $performanceConfig
   */
  public function setPerformanceConfig(PerformanceConfig $performanceConfig)
  {
    $this->performanceConfig = $performanceConfig;
  }
  /**
   * @return PerformanceConfig
   */
  public function getPerformanceConfig()
  {
    return $this->performanceConfig;
  }
  /**
   * Output only. The current migration job phase.
   *
   * Accepted values: PHASE_UNSPECIFIED, FULL_DUMP, CDC, PROMOTE_IN_PROGRESS,
   * WAITING_FOR_SOURCE_WRITES_TO_STOP, PREPARING_THE_DUMP, READY_FOR_PROMOTE
   *
   * @param self::PHASE_* $phase
   */
  public function setPhase($phase)
  {
    $this->phase = $phase;
  }
  /**
   * @return self::PHASE_*
   */
  public function getPhase()
  {
    return $this->phase;
  }
  /**
   * The details needed to communicate to the source over Reverse SSH tunnel
   * connectivity.
   *
   * @param ReverseSshConnectivity $reverseSshConnectivity
   */
  public function setReverseSshConnectivity(ReverseSshConnectivity $reverseSshConnectivity)
  {
    $this->reverseSshConnectivity = $reverseSshConnectivity;
  }
  /**
   * @return ReverseSshConnectivity
   */
  public function getReverseSshConnectivity()
  {
    return $this->reverseSshConnectivity;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Required. The resource name (URI) of the source connection profile.
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * The database engine type and provider of the source.
   *
   * @param DatabaseType $sourceDatabase
   */
  public function setSourceDatabase(DatabaseType $sourceDatabase)
  {
    $this->sourceDatabase = $sourceDatabase;
  }
  /**
   * @return DatabaseType
   */
  public function getSourceDatabase()
  {
    return $this->sourceDatabase;
  }
  /**
   * Optional. Configuration for SQL Server homogeneous migration.
   *
   * @param SqlServerHomogeneousMigrationJobConfig $sqlserverHomogeneousMigrationJobConfig
   */
  public function setSqlserverHomogeneousMigrationJobConfig(SqlServerHomogeneousMigrationJobConfig $sqlserverHomogeneousMigrationJobConfig)
  {
    $this->sqlserverHomogeneousMigrationJobConfig = $sqlserverHomogeneousMigrationJobConfig;
  }
  /**
   * @return SqlServerHomogeneousMigrationJobConfig
   */
  public function getSqlserverHomogeneousMigrationJobConfig()
  {
    return $this->sqlserverHomogeneousMigrationJobConfig;
  }
  /**
   * Configuration for heterogeneous **SQL Server to Cloud SQL for PostgreSQL**
   * migrations.
   *
   * @param SqlServerToPostgresConfig $sqlserverToPostgresConfig
   */
  public function setSqlserverToPostgresConfig(SqlServerToPostgresConfig $sqlserverToPostgresConfig)
  {
    $this->sqlserverToPostgresConfig = $sqlserverToPostgresConfig;
  }
  /**
   * @return SqlServerToPostgresConfig
   */
  public function getSqlserverToPostgresConfig()
  {
    return $this->sqlserverToPostgresConfig;
  }
  /**
   * The current migration job state.
   *
   * Accepted values: STATE_UNSPECIFIED, MAINTENANCE, DRAFT, CREATING,
   * NOT_STARTED, RUNNING, FAILED, COMPLETED, DELETING, STOPPING, STOPPED,
   * DELETED, UPDATING, STARTING, RESTARTING, RESUMING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * static ip connectivity data (default, no additional details needed).
   *
   * @param StaticIpConnectivity $staticIpConnectivity
   */
  public function setStaticIpConnectivity(StaticIpConnectivity $staticIpConnectivity)
  {
    $this->staticIpConnectivity = $staticIpConnectivity;
  }
  /**
   * @return StaticIpConnectivity
   */
  public function getStaticIpConnectivity()
  {
    return $this->staticIpConnectivity;
  }
  /**
   * Required. The migration job type.
   *
   * Accepted values: TYPE_UNSPECIFIED, ONE_TIME, CONTINUOUS
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
  /**
   * Output only. The timestamp when the migration job resource was last
   * updated. A timestamp in RFC3339 UTC "Zulu" format, accurate to nanoseconds.
   * Example: "2014-10-02T15:01:23.045123456Z".
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * The details of the VPC network that the source database is located in.
   *
   * @param VpcPeeringConnectivity $vpcPeeringConnectivity
   */
  public function setVpcPeeringConnectivity(VpcPeeringConnectivity $vpcPeeringConnectivity)
  {
    $this->vpcPeeringConnectivity = $vpcPeeringConnectivity;
  }
  /**
   * @return VpcPeeringConnectivity
   */
  public function getVpcPeeringConnectivity()
  {
    return $this->vpcPeeringConnectivity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MigrationJob::class, 'Google_Service_DatabaseMigrationService_MigrationJob');
