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

class Restore extends \Google\Collection
{
  /**
   * The Restore resource is in the process of being created.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Restore resource has been created and the associated RestoreJob
   * Kubernetes resource has been injected into target cluster.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The gkebackup agent in the cluster has begun executing the restore
   * operation.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The restore operation has completed successfully. Restored workloads may
   * not yet be operational.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The restore operation has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * This Restore resource is in the process of being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The Kubernetes resources created by this Restore are being validated.
   */
  public const STATE_VALIDATING = 'VALIDATING';
  protected $collection_key = 'volumeDataRestorePolicyOverrides';
  /**
   * Required. Immutable. A reference to the Backup used as the source from
   * which this Restore will restore. Note that this Backup must be a sub-
   * resource of the RestorePlan's backup_plan. Format:
   * `projects/locations/backupPlans/backups`.
   *
   * @var string
   */
  public $backup;
  /**
   * Output only. The target cluster into which this Restore will restore data.
   * Valid formats: - `projects/locations/clusters` - `projects/zones/clusters`
   * Inherited from parent RestorePlan's cluster value.
   *
   * @var string
   */
  public $cluster;
  /**
   * Output only. Timestamp of when the restore operation completed.
   *
   * @var string
   */
  public $completeTime;
  /**
   * Output only. The timestamp when this Restore resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. User specified descriptive string for this Restore.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a restore from overwriting each other.
   * It is strongly suggested that systems make use of the `etag` in the read-
   * modify-write cycle to perform restore updates in order to avoid race
   * conditions: An `etag` is returned in the response to `GetRestore`, and
   * systems are expected to put that etag in the request to `UpdateRestore` or
   * `DeleteRestore` to ensure that their change will be applied to the same
   * version of the resource.
   *
   * @var string
   */
  public $etag;
  protected $filterType = Filter::class;
  protected $filterDataType = '';
  /**
   * A set of custom labels supplied by user.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. The full name of the Restore resource. Format:
   * `projects/locations/restorePlans/restores`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Number of resources excluded during the restore execution.
   *
   * @var int
   */
  public $resourcesExcludedCount;
  /**
   * Output only. Number of resources that failed to be restored during the
   * restore execution.
   *
   * @var int
   */
  public $resourcesFailedCount;
  /**
   * Output only. Number of resources restored during the restore execution.
   *
   * @var int
   */
  public $resourcesRestoredCount;
  protected $restoreConfigType = RestoreConfig::class;
  protected $restoreConfigDataType = '';
  /**
   * Output only. The current state of the Restore.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Human-readable description of why the Restore is in its
   * current state. This field is only meant for human readability and should
   * not be used programmatically as this field is not guaranteed to be
   * consistent.
   *
   * @var string
   */
  public $stateReason;
  protected $troubleshootingInfoType = TroubleshootingInfo::class;
  protected $troubleshootingInfoDataType = '';
  /**
   * Output only. Server generated global unique identifier of
   * [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) format.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The timestamp when this Restore resource was last updated.
   *
   * @var string
   */
  public $updateTime;
  protected $volumeDataRestorePolicyOverridesType = VolumeDataRestorePolicyOverride::class;
  protected $volumeDataRestorePolicyOverridesDataType = 'array';
  /**
   * Output only. Number of volumes restored during the restore execution.
   *
   * @var int
   */
  public $volumesRestoredCount;

  /**
   * Required. Immutable. A reference to the Backup used as the source from
   * which this Restore will restore. Note that this Backup must be a sub-
   * resource of the RestorePlan's backup_plan. Format:
   * `projects/locations/backupPlans/backups`.
   *
   * @param string $backup
   */
  public function setBackup($backup)
  {
    $this->backup = $backup;
  }
  /**
   * @return string
   */
  public function getBackup()
  {
    return $this->backup;
  }
  /**
   * Output only. The target cluster into which this Restore will restore data.
   * Valid formats: - `projects/locations/clusters` - `projects/zones/clusters`
   * Inherited from parent RestorePlan's cluster value.
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Output only. Timestamp of when the restore operation completed.
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
   * Output only. The timestamp when this Restore resource was created.
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
   * Optional. User specified descriptive string for this Restore.
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
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a restore from overwriting each other.
   * It is strongly suggested that systems make use of the `etag` in the read-
   * modify-write cycle to perform restore updates in order to avoid race
   * conditions: An `etag` is returned in the response to `GetRestore`, and
   * systems are expected to put that etag in the request to `UpdateRestore` or
   * `DeleteRestore` to ensure that their change will be applied to the same
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
   * Optional. Immutable. Filters resources for `Restore`. If not specified, the
   * scope of the restore will remain the same as defined in the `RestorePlan`.
   * If this is specified and no resources are matched by the
   * `inclusion_filters` or everything is excluded by the `exclusion_filters`,
   * nothing will be restored. This filter can only be specified if the value of
   * namespaced_resource_restore_mode is set to `MERGE_SKIP_ON_CONFLICT`,
   * `MERGE_REPLACE_VOLUME_ON_CONFLICT` or `MERGE_REPLACE_ON_CONFLICT`.
   *
   * @param Filter $filter
   */
  public function setFilter(Filter $filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return Filter
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * A set of custom labels supplied by user.
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
   * Output only. Identifier. The full name of the Restore resource. Format:
   * `projects/locations/restorePlans/restores`
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
   * Output only. Number of resources excluded during the restore execution.
   *
   * @param int $resourcesExcludedCount
   */
  public function setResourcesExcludedCount($resourcesExcludedCount)
  {
    $this->resourcesExcludedCount = $resourcesExcludedCount;
  }
  /**
   * @return int
   */
  public function getResourcesExcludedCount()
  {
    return $this->resourcesExcludedCount;
  }
  /**
   * Output only. Number of resources that failed to be restored during the
   * restore execution.
   *
   * @param int $resourcesFailedCount
   */
  public function setResourcesFailedCount($resourcesFailedCount)
  {
    $this->resourcesFailedCount = $resourcesFailedCount;
  }
  /**
   * @return int
   */
  public function getResourcesFailedCount()
  {
    return $this->resourcesFailedCount;
  }
  /**
   * Output only. Number of resources restored during the restore execution.
   *
   * @param int $resourcesRestoredCount
   */
  public function setResourcesRestoredCount($resourcesRestoredCount)
  {
    $this->resourcesRestoredCount = $resourcesRestoredCount;
  }
  /**
   * @return int
   */
  public function getResourcesRestoredCount()
  {
    return $this->resourcesRestoredCount;
  }
  /**
   * Output only. Configuration of the Restore. Inherited from parent
   * RestorePlan's restore_config.
   *
   * @param RestoreConfig $restoreConfig
   */
  public function setRestoreConfig(RestoreConfig $restoreConfig)
  {
    $this->restoreConfig = $restoreConfig;
  }
  /**
   * @return RestoreConfig
   */
  public function getRestoreConfig()
  {
    return $this->restoreConfig;
  }
  /**
   * Output only. The current state of the Restore.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, IN_PROGRESS, SUCCEEDED,
   * FAILED, DELETING, VALIDATING
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
   * Output only. Human-readable description of why the Restore is in its
   * current state. This field is only meant for human readability and should
   * not be used programmatically as this field is not guaranteed to be
   * consistent.
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
   * [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) format.
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
   * Output only. The timestamp when this Restore resource was last updated.
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
   * Optional. Immutable. Overrides the volume data restore policies selected in
   * the Restore Config for override-scoped resources.
   *
   * @param VolumeDataRestorePolicyOverride[] $volumeDataRestorePolicyOverrides
   */
  public function setVolumeDataRestorePolicyOverrides($volumeDataRestorePolicyOverrides)
  {
    $this->volumeDataRestorePolicyOverrides = $volumeDataRestorePolicyOverrides;
  }
  /**
   * @return VolumeDataRestorePolicyOverride[]
   */
  public function getVolumeDataRestorePolicyOverrides()
  {
    return $this->volumeDataRestorePolicyOverrides;
  }
  /**
   * Output only. Number of volumes restored during the restore execution.
   *
   * @param int $volumesRestoredCount
   */
  public function setVolumesRestoredCount($volumesRestoredCount)
  {
    $this->volumesRestoredCount = $volumesRestoredCount;
  }
  /**
   * @return int
   */
  public function getVolumesRestoredCount()
  {
    return $this->volumesRestoredCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Restore::class, 'Google_Service_BackupforGKE_Restore');
