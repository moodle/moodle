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

namespace Google\Service\BackupforGKE;

class Backup extends \Google\Model
{
  /**
   * The Backup resource is in the process of being created.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Backup resource has been created and the associated BackupJob
   * Kubernetes resource has been injected into the source cluster.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The gkebackup agent in the cluster has begun executing the backup
   * operation.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The backup operation has completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The backup operation has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * This Backup resource (and its associated artifacts) is in the process of
   * being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Output only. If True, all namespaces were included in the Backup.
   *
   * @var bool
   */
  public $allNamespaces;
  protected $clusterMetadataType = ClusterMetadata::class;
  protected $clusterMetadataDataType = '';
  /**
   * Output only. Completion time of the Backup
   *
   * @var string
   */
  public $completeTime;
  /**
   * Output only. The size of the config backup in bytes.
   *
   * @var string
   */
  public $configBackupSizeBytes;
  /**
   * Output only. Whether or not the Backup contains Kubernetes Secrets.
   * Controlled by the parent BackupPlan's include_secrets value.
   *
   * @var bool
   */
  public $containsSecrets;
  /**
   * Output only. Whether or not the Backup contains volume data. Controlled by
   * the parent BackupPlan's include_volume_data value.
   *
   * @var bool
   */
  public $containsVolumeData;
  /**
   * Output only. The timestamp when this Backup resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Minimum age for this Backup (in days). If this field is set to a
   * non-zero value, the Backup will be "locked" against deletion (either manual
   * or automatic deletion) for the number of days provided (measured from the
   * creation time of the Backup). MUST be an integer value between 0-90
   * (inclusive). Defaults to parent BackupPlan's backup_delete_lock_days
   * setting and may only be increased (either at creation time or in a
   * subsequent update).
   *
   * @var int
   */
  public $deleteLockDays;
  /**
   * Output only. The time at which an existing delete lock will expire for this
   * backup (calculated from create_time + delete_lock_days).
   *
   * @var string
   */
  public $deleteLockExpireTime;
  /**
   * Optional. User specified descriptive string for this Backup.
   *
   * @var string
   */
  public $description;
  protected $encryptionKeyType = EncryptionKey::class;
  protected $encryptionKeyDataType = '';
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a backup from overwriting each other.
   * It is strongly suggested that systems make use of the `etag` in the read-
   * modify-write cycle to perform backup updates in order to avoid race
   * conditions: An `etag` is returned in the response to `GetBackup`, and
   * systems are expected to put that etag in the request to `UpdateBackup` or
   * `DeleteBackup` to ensure that their change will be applied to the same
   * version of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. A set of custom labels supplied by user.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. This flag indicates whether this Backup resource was created
   * manually by a user or via a schedule in the BackupPlan. A value of True
   * means that the Backup was created manually.
   *
   * @var bool
   */
  public $manual;
  /**
   * Output only. Identifier. The fully qualified name of the Backup.
   * `projects/locations/backupPlans/backups`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. If false, Backup will fail when Backup for GKE detects
   * Kubernetes configuration that is non-standard or requires additional setup
   * to restore. Inherited from the parent BackupPlan's permissive_mode value.
   *
   * @var bool
   */
  public $permissiveMode;
  /**
   * Output only. The total number of Kubernetes Pods contained in the Backup.
   *
   * @var int
   */
  public $podCount;
  /**
   * Output only. The total number of Kubernetes resources included in the
   * Backup.
   *
   * @var int
   */
  public $resourceCount;
  /**
   * Optional. The age (in days) after which this Backup will be automatically
   * deleted. Must be an integer value >= 0: - If 0, no automatic deletion will
   * occur for this Backup. - If not 0, this must be >= delete_lock_days and <=
   * 365. Once a Backup is created, this value may only be increased. Defaults
   * to the parent BackupPlan's backup_retain_days value.
   *
   * @var int
   */
  public $retainDays;
  /**
   * Output only. The time at which this Backup will be automatically deleted
   * (calculated from create_time + retain_days).
   *
   * @var string
   */
  public $retainExpireTime;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. [Output Only] Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $selectedApplicationsType = NamespacedNames::class;
  protected $selectedApplicationsDataType = '';
  protected $selectedNamespaceLabelsType = ResourceLabels::class;
  protected $selectedNamespaceLabelsDataType = '';
  protected $selectedNamespacesType = Namespaces::class;
  protected $selectedNamespacesDataType = '';
  /**
   * Output only. The total size of the Backup in bytes = config backup size +
   * sum(volume backup sizes)
   *
   * @var string
   */
  public $sizeBytes;
  /**
   * Output only. Current state of the Backup
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Human-readable description of why the backup is in the current
   * `state`. This field is only meant for human readability and should not be
   * used programmatically as this field is not guaranteed to be consistent.
   *
   * @var string
   */
  public $stateReason;
  protected $troubleshootingInfoType = TroubleshootingInfo::class;
  protected $troubleshootingInfoDataType = '';
  /**
   * Output only. Server generated global unique identifier of
   * [UUID4](https://en.wikipedia.org/wiki/Universally_unique_identifier)
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The timestamp when this Backup resource was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The total number of volume backups contained in the Backup.
   *
   * @var int
   */
  public $volumeCount;

  /**
   * Output only. If True, all namespaces were included in the Backup.
   *
   * @param bool $allNamespaces
   */
  public function setAllNamespaces($allNamespaces)
  {
    $this->allNamespaces = $allNamespaces;
  }
  /**
   * @return bool
   */
  public function getAllNamespaces()
  {
    return $this->allNamespaces;
  }
  /**
   * Output only. Information about the GKE cluster from which this Backup was
   * created.
   *
   * @param ClusterMetadata $clusterMetadata
   */
  public function setClusterMetadata(ClusterMetadata $clusterMetadata)
  {
    $this->clusterMetadata = $clusterMetadata;
  }
  /**
   * @return ClusterMetadata
   */
  public function getClusterMetadata()
  {
    return $this->clusterMetadata;
  }
  /**
   * Output only. Completion time of the Backup
   *
   * @param string $completeTime
   */
  public function setCompleteTime($completeTime)
  {
    $this->completeTime = $completeTime;
  }
  /**
   * @return string
   */
  public function getCompleteTime()
  {
    return $this->completeTime;
  }
  /**
   * Output only. The size of the config backup in bytes.
   *
   * @param string $configBackupSizeBytes
   */
  public function setConfigBackupSizeBytes($configBackupSizeBytes)
  {
    $this->configBackupSizeBytes = $configBackupSizeBytes;
  }
  /**
   * @return string
   */
  public function getConfigBackupSizeBytes()
  {
    return $this->configBackupSizeBytes;
  }
  /**
   * Output only. Whether or not the Backup contains Kubernetes Secrets.
   * Controlled by the parent BackupPlan's include_secrets value.
   *
   * @param bool $containsSecrets
   */
  public function setContainsSecrets($containsSecrets)
  {
    $this->containsSecrets = $containsSecrets;
  }
  /**
   * @return bool
   */
  public function getContainsSecrets()
  {
    return $this->containsSecrets;
  }
  /**
   * Output only. Whether or not the Backup contains volume data. Controlled by
   * the parent BackupPlan's include_volume_data value.
   *
   * @param bool $containsVolumeData
   */
  public function setContainsVolumeData($containsVolumeData)
  {
    $this->containsVolumeData = $containsVolumeData;
  }
  /**
   * @return bool
   */
  public function getContainsVolumeData()
  {
    return $this->containsVolumeData;
  }
  /**
   * Output only. The timestamp when this Backup resource was created.
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
   * Optional. Minimum age for this Backup (in days). If this field is set to a
   * non-zero value, the Backup will be "locked" against deletion (either manual
   * or automatic deletion) for the number of days provided (measured from the
   * creation time of the Backup). MUST be an integer value between 0-90
   * (inclusive). Defaults to parent BackupPlan's backup_delete_lock_days
   * setting and may only be increased (either at creation time or in a
   * subsequent update).
   *
   * @param int $deleteLockDays
   */
  public function setDeleteLockDays($deleteLockDays)
  {
    $this->deleteLockDays = $deleteLockDays;
  }
  /**
   * @return int
   */
  public function getDeleteLockDays()
  {
    return $this->deleteLockDays;
  }
  /**
   * Output only. The time at which an existing delete lock will expire for this
   * backup (calculated from create_time + delete_lock_days).
   *
   * @param string $deleteLockExpireTime
   */
  public function setDeleteLockExpireTime($deleteLockExpireTime)
  {
    $this->deleteLockExpireTime = $deleteLockExpireTime;
  }
  /**
   * @return string
   */
  public function getDeleteLockExpireTime()
  {
    return $this->deleteLockExpireTime;
  }
  /**
   * Optional. User specified descriptive string for this Backup.
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
   * Output only. The customer managed encryption key that was used to encrypt
   * the Backup's artifacts. Inherited from the parent BackupPlan's
   * encryption_key value.
   *
   * @param EncryptionKey $encryptionKey
   */
  public function setEncryptionKey(EncryptionKey $encryptionKey)
  {
    $this->encryptionKey = $encryptionKey;
  }
  /**
   * @return EncryptionKey
   */
  public function getEncryptionKey()
  {
    return $this->encryptionKey;
  }
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a backup from overwriting each other.
   * It is strongly suggested that systems make use of the `etag` in the read-
   * modify-write cycle to perform backup updates in order to avoid race
   * conditions: An `etag` is returned in the response to `GetBackup`, and
   * systems are expected to put that etag in the request to `UpdateBackup` or
   * `DeleteBackup` to ensure that their change will be applied to the same
   * version of the resource.
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
   * Optional. A set of custom labels supplied by user.
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
   * Output only. This flag indicates whether this Backup resource was created
   * manually by a user or via a schedule in the BackupPlan. A value of True
   * means that the Backup was created manually.
   *
   * @param bool $manual
   */
  public function setManual($manual)
  {
    $this->manual = $manual;
  }
  /**
   * @return bool
   */
  public function getManual()
  {
    return $this->manual;
  }
  /**
   * Output only. Identifier. The fully qualified name of the Backup.
   * `projects/locations/backupPlans/backups`
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
   * Output only. If false, Backup will fail when Backup for GKE detects
   * Kubernetes configuration that is non-standard or requires additional setup
   * to restore. Inherited from the parent BackupPlan's permissive_mode value.
   *
   * @param bool $permissiveMode
   */
  public function setPermissiveMode($permissiveMode)
  {
    $this->permissiveMode = $permissiveMode;
  }
  /**
   * @return bool
   */
  public function getPermissiveMode()
  {
    return $this->permissiveMode;
  }
  /**
   * Output only. The total number of Kubernetes Pods contained in the Backup.
   *
   * @param int $podCount
   */
  public function setPodCount($podCount)
  {
    $this->podCount = $podCount;
  }
  /**
   * @return int
   */
  public function getPodCount()
  {
    return $this->podCount;
  }
  /**
   * Output only. The total number of Kubernetes resources included in the
   * Backup.
   *
   * @param int $resourceCount
   */
  public function setResourceCount($resourceCount)
  {
    $this->resourceCount = $resourceCount;
  }
  /**
   * @return int
   */
  public function getResourceCount()
  {
    return $this->resourceCount;
  }
  /**
   * Optional. The age (in days) after which this Backup will be automatically
   * deleted. Must be an integer value >= 0: - If 0, no automatic deletion will
   * occur for this Backup. - If not 0, this must be >= delete_lock_days and <=
   * 365. Once a Backup is created, this value may only be increased. Defaults
   * to the parent BackupPlan's backup_retain_days value.
   *
   * @param int $retainDays
   */
  public function setRetainDays($retainDays)
  {
    $this->retainDays = $retainDays;
  }
  /**
   * @return int
   */
  public function getRetainDays()
  {
    return $this->retainDays;
  }
  /**
   * Output only. The time at which this Backup will be automatically deleted
   * (calculated from create_time + retain_days).
   *
   * @param string $retainExpireTime
   */
  public function setRetainExpireTime($retainExpireTime)
  {
    $this->retainExpireTime = $retainExpireTime;
  }
  /**
   * @return string
   */
  public function getRetainExpireTime()
  {
    return $this->retainExpireTime;
  }
  /**
   * Output only. [Output Only] Reserved for future use.
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
   * Output only. [Output Only] Reserved for future use.
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
   * Output only. If set, the list of ProtectedApplications whose resources were
   * included in the Backup.
   *
   * @param NamespacedNames $selectedApplications
   */
  public function setSelectedApplications(NamespacedNames $selectedApplications)
  {
    $this->selectedApplications = $selectedApplications;
  }
  /**
   * @return NamespacedNames
   */
  public function getSelectedApplications()
  {
    return $this->selectedApplications;
  }
  /**
   * Output only. If set, the list of labels whose constituent namespaces were
   * included in the Backup.
   *
   * @param ResourceLabels $selectedNamespaceLabels
   */
  public function setSelectedNamespaceLabels(ResourceLabels $selectedNamespaceLabels)
  {
    $this->selectedNamespaceLabels = $selectedNamespaceLabels;
  }
  /**
   * @return ResourceLabels
   */
  public function getSelectedNamespaceLabels()
  {
    return $this->selectedNamespaceLabels;
  }
  /**
   * Output only. If set, the list of namespaces that were included in the
   * Backup.
   *
   * @param Namespaces $selectedNamespaces
   */
  public function setSelectedNamespaces(Namespaces $selectedNamespaces)
  {
    $this->selectedNamespaces = $selectedNamespaces;
  }
  /**
   * @return Namespaces
   */
  public function getSelectedNamespaces()
  {
    return $this->selectedNamespaces;
  }
  /**
   * Output only. The total size of the Backup in bytes = config backup size +
   * sum(volume backup sizes)
   *
   * @param string $sizeBytes
   */
  public function setSizeBytes($sizeBytes)
  {
    $this->sizeBytes = $sizeBytes;
  }
  /**
   * @return string
   */
  public function getSizeBytes()
  {
    return $this->sizeBytes;
  }
  /**
   * Output only. Current state of the Backup
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, IN_PROGRESS, SUCCEEDED,
   * FAILED, DELETING
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
   * Output only. Human-readable description of why the backup is in the current
   * `state`. This field is only meant for human readability and should not be
   * used programmatically as this field is not guaranteed to be consistent.
   *
   * @param string $stateReason
   */
  public function setStateReason($stateReason)
  {
    $this->stateReason = $stateReason;
  }
  /**
   * @return string
   */
  public function getStateReason()
  {
    return $this->stateReason;
  }
  /**
   * Output only. Information about the troubleshooting steps which will provide
   * debugging information to the end users.
   *
   * @param TroubleshootingInfo $troubleshootingInfo
   */
  public function setTroubleshootingInfo(TroubleshootingInfo $troubleshootingInfo)
  {
    $this->troubleshootingInfo = $troubleshootingInfo;
  }
  /**
   * @return TroubleshootingInfo
   */
  public function getTroubleshootingInfo()
  {
    return $this->troubleshootingInfo;
  }
  /**
   * Output only. Server generated global unique identifier of
   * [UUID4](https://en.wikipedia.org/wiki/Universally_unique_identifier)
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The timestamp when this Backup resource was last updated.
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
   * Output only. The total number of volume backups contained in the Backup.
   *
   * @param int $volumeCount
   */
  public function setVolumeCount($volumeCount)
  {
    $this->volumeCount = $volumeCount;
  }
  /**
   * @return int
   */
  public function getVolumeCount()
  {
    return $this->volumeCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backup::class, 'Google_Service_BackupforGKE_Backup');
