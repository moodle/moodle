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

namespace Google\Service\VMMigrationService;

class MigratingVm extends \Google\Collection
{
  /**
   * The state was not sampled by the health checks yet.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The VM in the source is being verified.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The source VM was verified, and it's ready to start replication.
   */
  public const STATE_READY = 'READY';
  /**
   * Migration is going through the first sync cycle.
   */
  public const STATE_FIRST_SYNC = 'FIRST_SYNC';
  /**
   * The replication is active, and it's running or scheduled to run.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The source VM is being turned off, and a final replication is currently
   * running.
   */
  public const STATE_CUTTING_OVER = 'CUTTING_OVER';
  /**
   * The source VM was stopped and replicated. The replication is currently
   * paused.
   */
  public const STATE_CUTOVER = 'CUTOVER';
  /**
   * A cutover job is active and replication cycle is running the final sync.
   */
  public const STATE_FINAL_SYNC = 'FINAL_SYNC';
  /**
   * The replication was paused by the user and no cycles are scheduled to run.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The migrating VM is being finalized and migration resources are being
   * removed.
   */
  public const STATE_FINALIZING = 'FINALIZING';
  /**
   * The replication process is done. The migrating VM is finalized and no
   * longer consumes billable resources.
   */
  public const STATE_FINALIZED = 'FINALIZED';
  /**
   * The replication process encountered an unrecoverable error and was aborted.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The migrating VM has passed its expiration date. It might be possible to
   * bring it back to "Active" state by updating the TTL field. For more
   * information, see the documentation.
   */
  public const STATE_EXPIRED = 'EXPIRED';
  /**
   * The migrating VM's has been finalized and migration resources have been
   * removed.
   */
  public const STATE_FINALIZED_EXPIRED = 'FINALIZED_EXPIRED';
  protected $collection_key = 'recentCutoverJobs';
  protected $awsSourceVmDetailsType = AwsSourceVmDetails::class;
  protected $awsSourceVmDetailsDataType = '';
  protected $azureSourceVmDetailsType = AzureSourceVmDetails::class;
  protected $azureSourceVmDetailsDataType = '';
  protected $computeEngineDisksTargetDefaultsType = ComputeEngineDisksTargetDefaults::class;
  protected $computeEngineDisksTargetDefaultsDataType = '';
  protected $computeEngineTargetDefaultsType = ComputeEngineTargetDefaults::class;
  protected $computeEngineTargetDefaultsDataType = '';
  /**
   * Output only. The time the migrating VM was created (this refers to this
   * resource and not to the time it was installed in the source).
   *
   * @var string
   */
  public $createTime;
  protected $currentSyncInfoType = ReplicationCycle::class;
  protected $currentSyncInfoDataType = '';
  protected $cutoverForecastType = CutoverForecast::class;
  protected $cutoverForecastDataType = '';
  /**
   * The description attached to the migrating VM by the user.
   *
   * @var string
   */
  public $description;
  /**
   * The display name attached to the MigratingVm by the user.
   *
   * @var string
   */
  public $displayName;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  protected $expirationType = Expiration::class;
  protected $expirationDataType = '';
  /**
   * Output only. The group this migrating vm is included in, if any. The group
   * is represented by the full path of the appropriate Group resource.
   *
   * @var string
   */
  public $group;
  /**
   * The labels of the migrating VM.
   *
   * @var string[]
   */
  public $labels;
  protected $lastReplicationCycleType = ReplicationCycle::class;
  protected $lastReplicationCycleDataType = '';
  protected $lastSyncType = ReplicationSync::class;
  protected $lastSyncDataType = '';
  /**
   * Output only. The identifier of the MigratingVm.
   *
   * @var string
   */
  public $name;
  protected $policyType = SchedulePolicy::class;
  protected $policyDataType = '';
  protected $recentCloneJobsType = CloneJob::class;
  protected $recentCloneJobsDataType = 'array';
  protected $recentCutoverJobsType = CutoverJob::class;
  protected $recentCutoverJobsDataType = 'array';
  /**
   * The unique ID of the VM in the source. The VM's name in vSphere can be
   * changed, so this is not the VM's name but rather its moRef id. This id is
   * of the form vm-.
   *
   * @var string
   */
  public $sourceVmId;
  /**
   * Output only. State of the MigratingVm.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The last time the migrating VM state was updated.
   *
   * @var string
   */
  public $stateTime;
  /**
   * Output only. The last time the migrating VM resource was updated.
   *
   * @var string
   */
  public $updateTime;
  protected $vmwareSourceVmDetailsType = VmwareSourceVmDetails::class;
  protected $vmwareSourceVmDetailsDataType = '';

  /**
   * Output only. Details of the VM from an AWS source.
   *
   * @param AwsSourceVmDetails $awsSourceVmDetails
   */
  public function setAwsSourceVmDetails(AwsSourceVmDetails $awsSourceVmDetails)
  {
    $this->awsSourceVmDetails = $awsSourceVmDetails;
  }
  /**
   * @return AwsSourceVmDetails
   */
  public function getAwsSourceVmDetails()
  {
    return $this->awsSourceVmDetails;
  }
  /**
   * Output only. Details of the VM from an Azure source.
   *
   * @param AzureSourceVmDetails $azureSourceVmDetails
   */
  public function setAzureSourceVmDetails(AzureSourceVmDetails $azureSourceVmDetails)
  {
    $this->azureSourceVmDetails = $azureSourceVmDetails;
  }
  /**
   * @return AzureSourceVmDetails
   */
  public function getAzureSourceVmDetails()
  {
    return $this->azureSourceVmDetails;
  }
  /**
   * Details of the target Persistent Disks in Compute Engine.
   *
   * @param ComputeEngineDisksTargetDefaults $computeEngineDisksTargetDefaults
   */
  public function setComputeEngineDisksTargetDefaults(ComputeEngineDisksTargetDefaults $computeEngineDisksTargetDefaults)
  {
    $this->computeEngineDisksTargetDefaults = $computeEngineDisksTargetDefaults;
  }
  /**
   * @return ComputeEngineDisksTargetDefaults
   */
  public function getComputeEngineDisksTargetDefaults()
  {
    return $this->computeEngineDisksTargetDefaults;
  }
  /**
   * Details of the target VM in Compute Engine.
   *
   * @param ComputeEngineTargetDefaults $computeEngineTargetDefaults
   */
  public function setComputeEngineTargetDefaults(ComputeEngineTargetDefaults $computeEngineTargetDefaults)
  {
    $this->computeEngineTargetDefaults = $computeEngineTargetDefaults;
  }
  /**
   * @return ComputeEngineTargetDefaults
   */
  public function getComputeEngineTargetDefaults()
  {
    return $this->computeEngineTargetDefaults;
  }
  /**
   * Output only. The time the migrating VM was created (this refers to this
   * resource and not to the time it was installed in the source).
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
   * Output only. Details of the current running replication cycle.
   *
   * @param ReplicationCycle $currentSyncInfo
   */
  public function setCurrentSyncInfo(ReplicationCycle $currentSyncInfo)
  {
    $this->currentSyncInfo = $currentSyncInfo;
  }
  /**
   * @return ReplicationCycle
   */
  public function getCurrentSyncInfo()
  {
    return $this->currentSyncInfo;
  }
  /**
   * Output only. Provides details of future CutoverJobs of a MigratingVm. Set
   * to empty when cutover forecast is unavailable.
   *
   * @param CutoverForecast $cutoverForecast
   */
  public function setCutoverForecast(CutoverForecast $cutoverForecast)
  {
    $this->cutoverForecast = $cutoverForecast;
  }
  /**
   * @return CutoverForecast
   */
  public function getCutoverForecast()
  {
    return $this->cutoverForecast;
  }
  /**
   * The description attached to the migrating VM by the user.
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
   * The display name attached to the MigratingVm by the user.
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
   * Output only. Provides details on the state of the Migrating VM in case of
   * an error in replication.
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
   * Output only. Provides details about the expiration state of the migrating
   * VM.
   *
   * @param Expiration $expiration
   */
  public function setExpiration(Expiration $expiration)
  {
    $this->expiration = $expiration;
  }
  /**
   * @return Expiration
   */
  public function getExpiration()
  {
    return $this->expiration;
  }
  /**
   * Output only. The group this migrating vm is included in, if any. The group
   * is represented by the full path of the appropriate Group resource.
   *
   * @param string $group
   */
  public function setGroup($group)
  {
    $this->group = $group;
  }
  /**
   * @return string
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * The labels of the migrating VM.
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
   * Output only. Details of the last replication cycle. This will be updated
   * whenever a replication cycle is finished and is not to be confused with
   * last_sync which is only updated on successful replication cycles.
   *
   * @param ReplicationCycle $lastReplicationCycle
   */
  public function setLastReplicationCycle(ReplicationCycle $lastReplicationCycle)
  {
    $this->lastReplicationCycle = $lastReplicationCycle;
  }
  /**
   * @return ReplicationCycle
   */
  public function getLastReplicationCycle()
  {
    return $this->lastReplicationCycle;
  }
  /**
   * Output only. The most updated snapshot created time in the source that
   * finished replication.
   *
   * @param ReplicationSync $lastSync
   */
  public function setLastSync(ReplicationSync $lastSync)
  {
    $this->lastSync = $lastSync;
  }
  /**
   * @return ReplicationSync
   */
  public function getLastSync()
  {
    return $this->lastSync;
  }
  /**
   * Output only. The identifier of the MigratingVm.
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
   * The replication schedule policy.
   *
   * @param SchedulePolicy $policy
   */
  public function setPolicy(SchedulePolicy $policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return SchedulePolicy
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * Output only. The recent clone jobs performed on the migrating VM. This
   * field holds the vm's last completed clone job and the vm's running clone
   * job, if one exists. Note: To have this field populated you need to
   * explicitly request it via the "view" parameter of the Get/List request.
   *
   * @param CloneJob[] $recentCloneJobs
   */
  public function setRecentCloneJobs($recentCloneJobs)
  {
    $this->recentCloneJobs = $recentCloneJobs;
  }
  /**
   * @return CloneJob[]
   */
  public function getRecentCloneJobs()
  {
    return $this->recentCloneJobs;
  }
  /**
   * Output only. The recent cutover jobs performed on the migrating VM. This
   * field holds the vm's last completed cutover job and the vm's running
   * cutover job, if one exists. Note: To have this field populated you need to
   * explicitly request it via the "view" parameter of the Get/List request.
   *
   * @param CutoverJob[] $recentCutoverJobs
   */
  public function setRecentCutoverJobs($recentCutoverJobs)
  {
    $this->recentCutoverJobs = $recentCutoverJobs;
  }
  /**
   * @return CutoverJob[]
   */
  public function getRecentCutoverJobs()
  {
    return $this->recentCutoverJobs;
  }
  /**
   * The unique ID of the VM in the source. The VM's name in vSphere can be
   * changed, so this is not the VM's name but rather its moRef id. This id is
   * of the form vm-.
   *
   * @param string $sourceVmId
   */
  public function setSourceVmId($sourceVmId)
  {
    $this->sourceVmId = $sourceVmId;
  }
  /**
   * @return string
   */
  public function getSourceVmId()
  {
    return $this->sourceVmId;
  }
  /**
   * Output only. State of the MigratingVm.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, READY, FIRST_SYNC, ACTIVE,
   * CUTTING_OVER, CUTOVER, FINAL_SYNC, PAUSED, FINALIZING, FINALIZED, ERROR,
   * EXPIRED, FINALIZED_EXPIRED
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
   * Output only. The last time the migrating VM state was updated.
   *
   * @param string $stateTime
   */
  public function setStateTime($stateTime)
  {
    $this->stateTime = $stateTime;
  }
  /**
   * @return string
   */
  public function getStateTime()
  {
    return $this->stateTime;
  }
  /**
   * Output only. The last time the migrating VM resource was updated.
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
   * Output only. Details of the VM from a Vmware source.
   *
   * @param VmwareSourceVmDetails $vmwareSourceVmDetails
   */
  public function setVmwareSourceVmDetails(VmwareSourceVmDetails $vmwareSourceVmDetails)
  {
    $this->vmwareSourceVmDetails = $vmwareSourceVmDetails;
  }
  /**
   * @return VmwareSourceVmDetails
   */
  public function getVmwareSourceVmDetails()
  {
    return $this->vmwareSourceVmDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MigratingVm::class, 'Google_Service_VMMigrationService_MigratingVm');
