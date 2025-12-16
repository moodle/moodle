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

class Operation extends \Google\Model
{
  /**
   * Unknown operation type.
   */
  public const OPERATION_TYPE_SQL_OPERATION_TYPE_UNSPECIFIED = 'SQL_OPERATION_TYPE_UNSPECIFIED';
  /**
   * Imports data into a Cloud SQL instance.
   */
  public const OPERATION_TYPE_IMPORT = 'IMPORT';
  /**
   * Exports data from a Cloud SQL instance to a Cloud Storage bucket.
   */
  public const OPERATION_TYPE_EXPORT = 'EXPORT';
  /**
   * Creates a new Cloud SQL instance.
   */
  public const OPERATION_TYPE_CREATE = 'CREATE';
  /**
   * Updates the settings of a Cloud SQL instance.
   */
  public const OPERATION_TYPE_UPDATE = 'UPDATE';
  /**
   * Deletes a Cloud SQL instance.
   */
  public const OPERATION_TYPE_DELETE = 'DELETE';
  /**
   * Restarts the Cloud SQL instance.
   */
  public const OPERATION_TYPE_RESTART = 'RESTART';
  /**
   * @deprecated
   */
  public const OPERATION_TYPE_BACKUP = 'BACKUP';
  /**
   * @deprecated
   */
  public const OPERATION_TYPE_SNAPSHOT = 'SNAPSHOT';
  /**
   * Performs instance backup.
   */
  public const OPERATION_TYPE_BACKUP_VOLUME = 'BACKUP_VOLUME';
  /**
   * Deletes an instance backup.
   */
  public const OPERATION_TYPE_DELETE_VOLUME = 'DELETE_VOLUME';
  /**
   * Restores an instance backup.
   */
  public const OPERATION_TYPE_RESTORE_VOLUME = 'RESTORE_VOLUME';
  /**
   * Injects a privileged user in mysql for MOB instances.
   */
  public const OPERATION_TYPE_INJECT_USER = 'INJECT_USER';
  /**
   * Clones a Cloud SQL instance.
   */
  public const OPERATION_TYPE_CLONE = 'CLONE';
  /**
   * Stops replication on a Cloud SQL read replica instance.
   */
  public const OPERATION_TYPE_STOP_REPLICA = 'STOP_REPLICA';
  /**
   * Starts replication on a Cloud SQL read replica instance.
   */
  public const OPERATION_TYPE_START_REPLICA = 'START_REPLICA';
  /**
   * Promotes a Cloud SQL replica instance.
   */
  public const OPERATION_TYPE_PROMOTE_REPLICA = 'PROMOTE_REPLICA';
  /**
   * Creates a Cloud SQL replica instance.
   */
  public const OPERATION_TYPE_CREATE_REPLICA = 'CREATE_REPLICA';
  /**
   * Creates a new user in a Cloud SQL instance.
   */
  public const OPERATION_TYPE_CREATE_USER = 'CREATE_USER';
  /**
   * Deletes a user from a Cloud SQL instance.
   */
  public const OPERATION_TYPE_DELETE_USER = 'DELETE_USER';
  /**
   * Updates an existing user in a Cloud SQL instance.
   */
  public const OPERATION_TYPE_UPDATE_USER = 'UPDATE_USER';
  /**
   * Creates a database in the Cloud SQL instance.
   */
  public const OPERATION_TYPE_CREATE_DATABASE = 'CREATE_DATABASE';
  /**
   * Deletes a database in the Cloud SQL instance.
   */
  public const OPERATION_TYPE_DELETE_DATABASE = 'DELETE_DATABASE';
  /**
   * Updates a database in the Cloud SQL instance.
   */
  public const OPERATION_TYPE_UPDATE_DATABASE = 'UPDATE_DATABASE';
  /**
   * Performs failover of an HA-enabled Cloud SQL failover replica.
   */
  public const OPERATION_TYPE_FAILOVER = 'FAILOVER';
  /**
   * Deletes the backup taken by a backup run.
   */
  public const OPERATION_TYPE_DELETE_BACKUP = 'DELETE_BACKUP';
  public const OPERATION_TYPE_RECREATE_REPLICA = 'RECREATE_REPLICA';
  /**
   * Truncates a general or slow log table in MySQL.
   */
  public const OPERATION_TYPE_TRUNCATE_LOG = 'TRUNCATE_LOG';
  /**
   * Demotes the stand-alone instance to be a Cloud SQL read replica for an
   * external database server.
   */
  public const OPERATION_TYPE_DEMOTE_MASTER = 'DEMOTE_MASTER';
  /**
   * Indicates that the instance is currently in maintenance. Maintenance
   * typically causes the instance to be unavailable for 1-3 minutes.
   */
  public const OPERATION_TYPE_MAINTENANCE = 'MAINTENANCE';
  /**
   * This field is deprecated, and will be removed in future version of API.
   *
   * @deprecated
   */
  public const OPERATION_TYPE_ENABLE_PRIVATE_IP = 'ENABLE_PRIVATE_IP';
  /**
   * @deprecated
   */
  public const OPERATION_TYPE_DEFER_MAINTENANCE = 'DEFER_MAINTENANCE';
  /**
   * Creates clone instance.
   *
   * @deprecated
   */
  public const OPERATION_TYPE_CREATE_CLONE = 'CREATE_CLONE';
  /**
   * Reschedule maintenance to another time.
   */
  public const OPERATION_TYPE_RESCHEDULE_MAINTENANCE = 'RESCHEDULE_MAINTENANCE';
  /**
   * Starts external sync of a Cloud SQL EM replica to an external primary
   * instance.
   */
  public const OPERATION_TYPE_START_EXTERNAL_SYNC = 'START_EXTERNAL_SYNC';
  /**
   * Recovers logs from an instance's old data disk.
   */
  public const OPERATION_TYPE_LOG_CLEANUP = 'LOG_CLEANUP';
  /**
   * Performs auto-restart of an HA-enabled Cloud SQL database for auto
   * recovery.
   */
  public const OPERATION_TYPE_AUTO_RESTART = 'AUTO_RESTART';
  /**
   * Re-encrypts CMEK instances with latest key version.
   */
  public const OPERATION_TYPE_REENCRYPT = 'REENCRYPT';
  /**
   * Switches the roles of the primary and replica pair. The target instance
   * should be the replica.
   */
  public const OPERATION_TYPE_SWITCHOVER = 'SWITCHOVER';
  /**
   * Update a backup.
   */
  public const OPERATION_TYPE_UPDATE_BACKUP = 'UPDATE_BACKUP';
  /**
   * Acquire a lease for the setup of SQL Server Reporting Services (SSRS).
   */
  public const OPERATION_TYPE_ACQUIRE_SSRS_LEASE = 'ACQUIRE_SSRS_LEASE';
  /**
   * Release a lease for the setup of SQL Server Reporting Services (SSRS).
   */
  public const OPERATION_TYPE_RELEASE_SSRS_LEASE = 'RELEASE_SSRS_LEASE';
  /**
   * Reconfigures old primary after a promote replica operation. Effect of a
   * promote operation to the old primary is executed in this operation,
   * asynchronously from the promote replica operation executed to the replica.
   */
  public const OPERATION_TYPE_RECONFIGURE_OLD_PRIMARY = 'RECONFIGURE_OLD_PRIMARY';
  /**
   * Indicates that the instance, its read replicas, and its cascading replicas
   * are in maintenance. Maintenance typically gets initiated on groups of
   * replicas first, followed by the primary instance. For each instance,
   * maintenance typically causes the instance to be unavailable for 1-3
   * minutes.
   *
   * @deprecated
   */
  public const OPERATION_TYPE_CLUSTER_MAINTENANCE = 'CLUSTER_MAINTENANCE';
  /**
   * Indicates that the instance (and any of its replicas) are currently in
   * maintenance. This is initiated as a self-service request by using SSM.
   * Maintenance typically causes the instance to be unavailable for 1-3
   * minutes.
   *
   * @deprecated
   */
  public const OPERATION_TYPE_SELF_SERVICE_MAINTENANCE = 'SELF_SERVICE_MAINTENANCE';
  /**
   * Switches a primary instance to a replica. This operation runs as part of a
   * switchover operation to the original primary instance.
   */
  public const OPERATION_TYPE_SWITCHOVER_TO_REPLICA = 'SWITCHOVER_TO_REPLICA';
  /**
   * Updates the major version of a Cloud SQL instance.
   */
  public const OPERATION_TYPE_MAJOR_VERSION_UPGRADE = 'MAJOR_VERSION_UPGRADE';
  /**
   * Deprecated: ADVANCED_BACKUP is deprecated. Use ENHANCED_BACKUP instead.
   *
   * @deprecated
   */
  public const OPERATION_TYPE_ADVANCED_BACKUP = 'ADVANCED_BACKUP';
  /**
   * Changes the BackupTier of a Cloud SQL instance.
   */
  public const OPERATION_TYPE_MANAGE_BACKUP = 'MANAGE_BACKUP';
  /**
   * Creates a backup for an Enhanced BackupTier Cloud SQL instance.
   */
  public const OPERATION_TYPE_ENHANCED_BACKUP = 'ENHANCED_BACKUP';
  /**
   * Repairs entire read pool or specified read pool nodes in the read pool.
   */
  public const OPERATION_TYPE_REPAIR_READ_POOL = 'REPAIR_READ_POOL';
  /**
   * Creates a Cloud SQL read pool instance.
   */
  public const OPERATION_TYPE_CREATE_READ_POOL = 'CREATE_READ_POOL';
  /**
   * The state of the operation is unknown.
   */
  public const STATUS_SQL_OPERATION_STATUS_UNSPECIFIED = 'SQL_OPERATION_STATUS_UNSPECIFIED';
  /**
   * The operation has been queued, but has not started yet.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * The operation is running.
   */
  public const STATUS_RUNNING = 'RUNNING';
  /**
   * The operation completed.
   */
  public const STATUS_DONE = 'DONE';
  protected $acquireSsrsLeaseContextType = AcquireSsrsLeaseContext::class;
  protected $acquireSsrsLeaseContextDataType = '';
  protected $apiWarningType = ApiWarning::class;
  protected $apiWarningDataType = '';
  protected $backupContextType = BackupContext::class;
  protected $backupContextDataType = '';
  /**
   * The time this operation finished in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = OperationErrors::class;
  protected $errorDataType = '';
  protected $exportContextType = ExportContext::class;
  protected $exportContextDataType = '';
  protected $importContextType = ImportContext::class;
  protected $importContextDataType = '';
  /**
   * The time this operation was enqueued in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @var string
   */
  public $insertTime;
  /**
   * This is always `sql#operation`.
   *
   * @var string
   */
  public $kind;
  /**
   * An identifier that uniquely identifies the operation. You can use this
   * identifier to retrieve the Operations resource that has information about
   * the operation.
   *
   * @var string
   */
  public $name;
  /**
   * The type of the operation. Valid values are: * `CREATE` * `DELETE` *
   * `UPDATE` * `RESTART` * `IMPORT` * `EXPORT` * `BACKUP_VOLUME` *
   * `RESTORE_VOLUME` * `CREATE_USER` * `DELETE_USER` * `CREATE_DATABASE` *
   * `DELETE_DATABASE`
   *
   * @var string
   */
  public $operationType;
  protected $preCheckMajorVersionUpgradeContextType = PreCheckMajorVersionUpgradeContext::class;
  protected $preCheckMajorVersionUpgradeContextDataType = '';
  /**
   * The URI of this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The time this operation actually started in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @var string
   */
  public $startTime;
  /**
   * The status of an operation.
   *
   * @var string
   */
  public $status;
  protected $subOperationTypeType = SqlSubOperationType::class;
  protected $subOperationTypeDataType = '';
  /**
   * Name of the resource on which this operation runs.
   *
   * @var string
   */
  public $targetId;
  /**
   * @var string
   */
  public $targetLink;
  /**
   * The project ID of the target instance related to this operation.
   *
   * @var string
   */
  public $targetProject;
  /**
   * The email address of the user who initiated this operation.
   *
   * @var string
   */
  public $user;

  /**
   * The context for acquire SSRS lease operation, if applicable.
   *
   * @param AcquireSsrsLeaseContext $acquireSsrsLeaseContext
   */
  public function setAcquireSsrsLeaseContext(AcquireSsrsLeaseContext $acquireSsrsLeaseContext)
  {
    $this->acquireSsrsLeaseContext = $acquireSsrsLeaseContext;
  }
  /**
   * @return AcquireSsrsLeaseContext
   */
  public function getAcquireSsrsLeaseContext()
  {
    return $this->acquireSsrsLeaseContext;
  }
  /**
   * An Admin API warning message.
   *
   * @param ApiWarning $apiWarning
   */
  public function setApiWarning(ApiWarning $apiWarning)
  {
    $this->apiWarning = $apiWarning;
  }
  /**
   * @return ApiWarning
   */
  public function getApiWarning()
  {
    return $this->apiWarning;
  }
  /**
   * The context for backup operation, if applicable.
   *
   * @param BackupContext $backupContext
   */
  public function setBackupContext(BackupContext $backupContext)
  {
    $this->backupContext = $backupContext;
  }
  /**
   * @return BackupContext
   */
  public function getBackupContext()
  {
    return $this->backupContext;
  }
  /**
   * The time this operation finished in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
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
   * If errors occurred during processing of this operation, this field will be
   * populated.
   *
   * @param OperationErrors $error
   */
  public function setError(OperationErrors $error)
  {
    $this->error = $error;
  }
  /**
   * @return OperationErrors
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The context for export operation, if applicable.
   *
   * @param ExportContext $exportContext
   */
  public function setExportContext(ExportContext $exportContext)
  {
    $this->exportContext = $exportContext;
  }
  /**
   * @return ExportContext
   */
  public function getExportContext()
  {
    return $this->exportContext;
  }
  /**
   * The context for import operation, if applicable.
   *
   * @param ImportContext $importContext
   */
  public function setImportContext(ImportContext $importContext)
  {
    $this->importContext = $importContext;
  }
  /**
   * @return ImportContext
   */
  public function getImportContext()
  {
    return $this->importContext;
  }
  /**
   * The time this operation was enqueued in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @param string $insertTime
   */
  public function setInsertTime($insertTime)
  {
    $this->insertTime = $insertTime;
  }
  /**
   * @return string
   */
  public function getInsertTime()
  {
    return $this->insertTime;
  }
  /**
   * This is always `sql#operation`.
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
   * An identifier that uniquely identifies the operation. You can use this
   * identifier to retrieve the Operations resource that has information about
   * the operation.
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
   * The type of the operation. Valid values are: * `CREATE` * `DELETE` *
   * `UPDATE` * `RESTART` * `IMPORT` * `EXPORT` * `BACKUP_VOLUME` *
   * `RESTORE_VOLUME` * `CREATE_USER` * `DELETE_USER` * `CREATE_DATABASE` *
   * `DELETE_DATABASE`
   *
   * Accepted values: SQL_OPERATION_TYPE_UNSPECIFIED, IMPORT, EXPORT, CREATE,
   * UPDATE, DELETE, RESTART, BACKUP, SNAPSHOT, BACKUP_VOLUME, DELETE_VOLUME,
   * RESTORE_VOLUME, INJECT_USER, CLONE, STOP_REPLICA, START_REPLICA,
   * PROMOTE_REPLICA, CREATE_REPLICA, CREATE_USER, DELETE_USER, UPDATE_USER,
   * CREATE_DATABASE, DELETE_DATABASE, UPDATE_DATABASE, FAILOVER, DELETE_BACKUP,
   * RECREATE_REPLICA, TRUNCATE_LOG, DEMOTE_MASTER, MAINTENANCE,
   * ENABLE_PRIVATE_IP, DEFER_MAINTENANCE, CREATE_CLONE, RESCHEDULE_MAINTENANCE,
   * START_EXTERNAL_SYNC, LOG_CLEANUP, AUTO_RESTART, REENCRYPT, SWITCHOVER,
   * UPDATE_BACKUP, ACQUIRE_SSRS_LEASE, RELEASE_SSRS_LEASE,
   * RECONFIGURE_OLD_PRIMARY, CLUSTER_MAINTENANCE, SELF_SERVICE_MAINTENANCE,
   * SWITCHOVER_TO_REPLICA, MAJOR_VERSION_UPGRADE, ADVANCED_BACKUP,
   * MANAGE_BACKUP, ENHANCED_BACKUP, REPAIR_READ_POOL, CREATE_READ_POOL
   *
   * @param self::OPERATION_TYPE_* $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return self::OPERATION_TYPE_*
   */
  public function getOperationType()
  {
    return $this->operationType;
  }
  /**
   * This field is only populated when the operation_type is
   * PRE_CHECK_MAJOR_VERSION_UPGRADE. The PreCheckMajorVersionUpgradeContext
   * message itself contains the details for that pre-check, such as the target
   * database version for the upgrade and the results of the check (including
   * any warnings or errors found).
   *
   * @param PreCheckMajorVersionUpgradeContext $preCheckMajorVersionUpgradeContext
   */
  public function setPreCheckMajorVersionUpgradeContext(PreCheckMajorVersionUpgradeContext $preCheckMajorVersionUpgradeContext)
  {
    $this->preCheckMajorVersionUpgradeContext = $preCheckMajorVersionUpgradeContext;
  }
  /**
   * @return PreCheckMajorVersionUpgradeContext
   */
  public function getPreCheckMajorVersionUpgradeContext()
  {
    return $this->preCheckMajorVersionUpgradeContext;
  }
  /**
   * The URI of this resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The time this operation actually started in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The status of an operation.
   *
   * Accepted values: SQL_OPERATION_STATUS_UNSPECIFIED, PENDING, RUNNING, DONE
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Optional. The sub operation based on the operation type.
   *
   * @param SqlSubOperationType $subOperationType
   */
  public function setSubOperationType(SqlSubOperationType $subOperationType)
  {
    $this->subOperationType = $subOperationType;
  }
  /**
   * @return SqlSubOperationType
   */
  public function getSubOperationType()
  {
    return $this->subOperationType;
  }
  /**
   * Name of the resource on which this operation runs.
   *
   * @param string $targetId
   */
  public function setTargetId($targetId)
  {
    $this->targetId = $targetId;
  }
  /**
   * @return string
   */
  public function getTargetId()
  {
    return $this->targetId;
  }
  /**
   * @param string $targetLink
   */
  public function setTargetLink($targetLink)
  {
    $this->targetLink = $targetLink;
  }
  /**
   * @return string
   */
  public function getTargetLink()
  {
    return $this->targetLink;
  }
  /**
   * The project ID of the target instance related to this operation.
   *
   * @param string $targetProject
   */
  public function setTargetProject($targetProject)
  {
    $this->targetProject = $targetProject;
  }
  /**
   * @return string
   */
  public function getTargetProject()
  {
    return $this->targetProject;
  }
  /**
   * The email address of the user who initiated this operation.
   *
   * @param string $user
   */
  public function setUser($user)
  {
    $this->user = $user;
  }
  /**
   * @return string
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Operation::class, 'Google_Service_SQLAdmin_Operation');
