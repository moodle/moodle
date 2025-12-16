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

namespace Google\Service\Backupdr;

class Backup extends \Google\Collection
{
  /**
   * Inheritance behavior not set. This will default to
   * `INHERIT_VAULT_RETENTION`.
   */
  public const BACKUP_RETENTION_INHERITANCE_BACKUP_RETENTION_INHERITANCE_UNSPECIFIED = 'BACKUP_RETENTION_INHERITANCE_UNSPECIFIED';
  /**
   * The enforced retention end time of a backup will be inherited from the
   * backup vault's `backup_minimum_enforced_retention_duration` field. This is
   * the default behavior.
   */
  public const BACKUP_RETENTION_INHERITANCE_INHERIT_VAULT_RETENTION = 'INHERIT_VAULT_RETENTION';
  /**
   * The enforced retention end time of a backup will always match the expire
   * time of the backup. If this is set, the backup's enforced retention end
   * time will be set to match the expire time during creation of the backup.
   * When updating, the ERET and expire time must be updated together and have
   * the same value. Invalid update requests will be rejected by the server.
   */
  public const BACKUP_RETENTION_INHERITANCE_MATCH_BACKUP_EXPIRE_TIME = 'MATCH_BACKUP_EXPIRE_TIME';
  /**
   * Backup type is unspecified.
   */
  public const BACKUP_TYPE_BACKUP_TYPE_UNSPECIFIED = 'BACKUP_TYPE_UNSPECIFIED';
  /**
   * Scheduled backup.
   */
  public const BACKUP_TYPE_SCHEDULED = 'SCHEDULED';
  /**
   * On demand backup.
   */
  public const BACKUP_TYPE_ON_DEMAND = 'ON_DEMAND';
  /**
   * Operational backup.
   */
  public const BACKUP_TYPE_ON_DEMAND_OPERATIONAL = 'ON_DEMAND_OPERATIONAL';
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The backup is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The backup has been created and is fully usable.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The backup is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The backup is experiencing an issue and might be unusable.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The backup is being uploaded.
   */
  public const STATE_UPLOADING = 'UPLOADING';
  protected $collection_key = 'serviceLocks';
  protected $alloyDbBackupPropertiesType = AlloyDbClusterBackupProperties::class;
  protected $alloyDbBackupPropertiesDataType = '';
  protected $backupApplianceBackupPropertiesType = BackupApplianceBackupProperties::class;
  protected $backupApplianceBackupPropertiesDataType = '';
  protected $backupApplianceLocksType = BackupLock::class;
  protected $backupApplianceLocksDataType = 'array';
  /**
   * Output only. Setting for how the enforced retention end time is inherited.
   * This value is copied from this backup's BackupVault.
   *
   * @var string
   */
  public $backupRetentionInheritance;
  /**
   * Output only. Type of the backup, unspecified, scheduled or ondemand.
   *
   * @var string
   */
  public $backupType;
  protected $cloudSqlInstanceBackupPropertiesType = CloudSqlInstanceBackupProperties::class;
  protected $cloudSqlInstanceBackupPropertiesDataType = '';
  protected $computeInstanceBackupPropertiesType = ComputeInstanceBackupProperties::class;
  protected $computeInstanceBackupPropertiesDataType = '';
  /**
   * Output only. The point in time when this backup was captured from the
   * source.
   *
   * @var string
   */
  public $consistencyTime;
  /**
   * Output only. The time when the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The description of the Backup instance (2048 characters or
   * less).
   *
   * @var string
   */
  public $description;
  protected $diskBackupPropertiesType = DiskBackupProperties::class;
  protected $diskBackupPropertiesDataType = '';
  /**
   * Optional. The backup can not be deleted before this time.
   *
   * @var string
   */
  public $enforcedRetentionEndTime;
  /**
   * Optional. Server specified ETag to prevent updates from overwriting each
   * other.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. When this backup is automatically expired.
   *
   * @var string
   */
  public $expireTime;
  protected $gcpBackupPlanInfoType = GCPBackupPlanInfo::class;
  protected $gcpBackupPlanInfoDataType = '';
  protected $gcpResourceType = BackupGcpResource::class;
  protected $gcpResourceDataType = '';
  /**
   * Optional. Output only. The list of KMS key versions used to encrypt the
   * backup.
   *
   * @var string[]
   */
  public $kmsKeyVersions;
  /**
   * Optional. Resource labels to represent user provided metadata. No labels
   * currently defined.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. Name of the backup to create. It must have the for
   * mat`"projects//locations//backupVaults//dataSources/{datasource}/backups/{b
   * ackup}"`. `{backup}` cannot be changed after creation. It must be between
   * 3-63 characters long and must be unique within the datasource.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. source resource size in bytes at the time of the backup.
   *
   * @var string
   */
  public $resourceSizeBytes;
  /**
   * Optional. Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Optional. Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $serviceLocksType = BackupLock::class;
  protected $serviceLocksDataType = 'array';
  /**
   * Output only. The Backup resource instance state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time when the instance was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. AlloyDB specific backup properties.
   *
   * @param AlloyDbClusterBackupProperties $alloyDbBackupProperties
   */
  public function setAlloyDbBackupProperties(AlloyDbClusterBackupProperties $alloyDbBackupProperties)
  {
    $this->alloyDbBackupProperties = $alloyDbBackupProperties;
  }
  /**
   * @return AlloyDbClusterBackupProperties
   */
  public function getAlloyDbBackupProperties()
  {
    return $this->alloyDbBackupProperties;
  }
  /**
   * Output only. Backup Appliance specific backup properties.
   *
   * @param BackupApplianceBackupProperties $backupApplianceBackupProperties
   */
  public function setBackupApplianceBackupProperties(BackupApplianceBackupProperties $backupApplianceBackupProperties)
  {
    $this->backupApplianceBackupProperties = $backupApplianceBackupProperties;
  }
  /**
   * @return BackupApplianceBackupProperties
   */
  public function getBackupApplianceBackupProperties()
  {
    return $this->backupApplianceBackupProperties;
  }
  /**
   * Optional. The list of BackupLocks taken by the accessor Backup Appliance.
   *
   * @param BackupLock[] $backupApplianceLocks
   */
  public function setBackupApplianceLocks($backupApplianceLocks)
  {
    $this->backupApplianceLocks = $backupApplianceLocks;
  }
  /**
   * @return BackupLock[]
   */
  public function getBackupApplianceLocks()
  {
    return $this->backupApplianceLocks;
  }
  /**
   * Output only. Setting for how the enforced retention end time is inherited.
   * This value is copied from this backup's BackupVault.
   *
   * Accepted values: BACKUP_RETENTION_INHERITANCE_UNSPECIFIED,
   * INHERIT_VAULT_RETENTION, MATCH_BACKUP_EXPIRE_TIME
   *
   * @param self::BACKUP_RETENTION_INHERITANCE_* $backupRetentionInheritance
   */
  public function setBackupRetentionInheritance($backupRetentionInheritance)
  {
    $this->backupRetentionInheritance = $backupRetentionInheritance;
  }
  /**
   * @return self::BACKUP_RETENTION_INHERITANCE_*
   */
  public function getBackupRetentionInheritance()
  {
    return $this->backupRetentionInheritance;
  }
  /**
   * Output only. Type of the backup, unspecified, scheduled or ondemand.
   *
   * Accepted values: BACKUP_TYPE_UNSPECIFIED, SCHEDULED, ON_DEMAND,
   * ON_DEMAND_OPERATIONAL
   *
   * @param self::BACKUP_TYPE_* $backupType
   */
  public function setBackupType($backupType)
  {
    $this->backupType = $backupType;
  }
  /**
   * @return self::BACKUP_TYPE_*
   */
  public function getBackupType()
  {
    return $this->backupType;
  }
  /**
   * Output only. Cloud SQL specific backup properties.
   *
   * @param CloudSqlInstanceBackupProperties $cloudSqlInstanceBackupProperties
   */
  public function setCloudSqlInstanceBackupProperties(CloudSqlInstanceBackupProperties $cloudSqlInstanceBackupProperties)
  {
    $this->cloudSqlInstanceBackupProperties = $cloudSqlInstanceBackupProperties;
  }
  /**
   * @return CloudSqlInstanceBackupProperties
   */
  public function getCloudSqlInstanceBackupProperties()
  {
    return $this->cloudSqlInstanceBackupProperties;
  }
  /**
   * Output only. Compute Engine specific backup properties.
   *
   * @param ComputeInstanceBackupProperties $computeInstanceBackupProperties
   */
  public function setComputeInstanceBackupProperties(ComputeInstanceBackupProperties $computeInstanceBackupProperties)
  {
    $this->computeInstanceBackupProperties = $computeInstanceBackupProperties;
  }
  /**
   * @return ComputeInstanceBackupProperties
   */
  public function getComputeInstanceBackupProperties()
  {
    return $this->computeInstanceBackupProperties;
  }
  /**
   * Output only. The point in time when this backup was captured from the
   * source.
   *
   * @param string $consistencyTime
   */
  public function setConsistencyTime($consistencyTime)
  {
    $this->consistencyTime = $consistencyTime;
  }
  /**
   * @return string
   */
  public function getConsistencyTime()
  {
    return $this->consistencyTime;
  }
  /**
   * Output only. The time when the instance was created.
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
   * Output only. The description of the Backup instance (2048 characters or
   * less).
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. Disk specific backup properties.
   *
   * @param DiskBackupProperties $diskBackupProperties
   */
  public function setDiskBackupProperties(DiskBackupProperties $diskBackupProperties)
  {
    $this->diskBackupProperties = $diskBackupProperties;
  }
  /**
   * @return DiskBackupProperties
   */
  public function getDiskBackupProperties()
  {
    return $this->diskBackupProperties;
  }
  /**
   * Optional. The backup can not be deleted before this time.
   *
   * @param string $enforcedRetentionEndTime
   */
  public function setEnforcedRetentionEndTime($enforcedRetentionEndTime)
  {
    $this->enforcedRetentionEndTime = $enforcedRetentionEndTime;
  }
  /**
   * @return string
   */
  public function getEnforcedRetentionEndTime()
  {
    return $this->enforcedRetentionEndTime;
  }
  /**
   * Optional. Server specified ETag to prevent updates from overwriting each
   * other.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. When this backup is automatically expired.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. Configuration for a Google Cloud resource.
   *
   * @param GCPBackupPlanInfo $gcpBackupPlanInfo
   */
  public function setGcpBackupPlanInfo(GCPBackupPlanInfo $gcpBackupPlanInfo)
  {
    $this->gcpBackupPlanInfo = $gcpBackupPlanInfo;
  }
  /**
   * @return GCPBackupPlanInfo
   */
  public function getGcpBackupPlanInfo()
  {
    return $this->gcpBackupPlanInfo;
  }
  /**
   * Output only. Unique identifier of the GCP resource that is being backed up.
   *
   * @param BackupGcpResource $gcpResource
   */
  public function setGcpResource(BackupGcpResource $gcpResource)
  {
    $this->gcpResource = $gcpResource;
  }
  /**
   * @return BackupGcpResource
   */
  public function getGcpResource()
  {
    return $this->gcpResource;
  }
  /**
   * Optional. Output only. The list of KMS key versions used to encrypt the
   * backup.
   *
   * @param string[] $kmsKeyVersions
   */
  public function setKmsKeyVersions($kmsKeyVersions)
  {
    $this->kmsKeyVersions = $kmsKeyVersions;
  }
  /**
   * @return string[]
   */
  public function getKmsKeyVersions()
  {
    return $this->kmsKeyVersions;
  }
  /**
   * Optional. Resource labels to represent user provided metadata. No labels
   * currently defined.
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
   * Output only. Identifier. Name of the backup to create. It must have the for
   * mat`"projects//locations//backupVaults//dataSources/{datasource}/backups/{b
   * ackup}"`. `{backup}` cannot be changed after creation. It must be between
   * 3-63 characters long and must be unique within the datasource.
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
   * Output only. source resource size in bytes at the time of the backup.
   *
   * @param string $resourceSizeBytes
   */
  public function setResourceSizeBytes($resourceSizeBytes)
  {
    $this->resourceSizeBytes = $resourceSizeBytes;
  }
  /**
   * @return string
   */
  public function getResourceSizeBytes()
  {
    return $this->resourceSizeBytes;
  }
  /**
   * Optional. Output only. Reserved for future use.
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
   * Optional. Output only. Reserved for future use.
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
   * Output only. The list of BackupLocks taken by the service to prevent the
   * deletion of the backup.
   *
   * @param BackupLock[] $serviceLocks
   */
  public function setServiceLocks($serviceLocks)
  {
    $this->serviceLocks = $serviceLocks;
  }
  /**
   * @return BackupLock[]
   */
  public function getServiceLocks()
  {
    return $this->serviceLocks;
  }
  /**
   * Output only. The Backup resource instance state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, ERROR,
   * UPLOADING
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
   * Output only. The time when the instance was updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backup::class, 'Google_Service_Backupdr_Backup');
