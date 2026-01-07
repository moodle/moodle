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

class BackupPlan extends \Google\Model
{
  /**
   * Default first value for Enums.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Waiting for cluster state to be RUNNING.
   */
  public const STATE_CLUSTER_PENDING = 'CLUSTER_PENDING';
  /**
   * The BackupPlan is in the process of being created.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The BackupPlan has successfully been created and is ready for Backups.
   */
  public const STATE_READY = 'READY';
  /**
   * BackupPlan creation has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The BackupPlan has been deactivated.
   */
  public const STATE_DEACTIVATED = 'DEACTIVATED';
  /**
   * The BackupPlan is in the process of being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Output only. The fully qualified name of the BackupChannel to be used to
   * create a backup. This field is set only if the cluster being backed up is
   * in a different project. `projects/locations/backupChannels`
   *
   * @var string
   */
  public $backupChannel;
  protected $backupConfigType = BackupConfig::class;
  protected $backupConfigDataType = '';
  protected $backupScheduleType = Schedule::class;
  protected $backupScheduleDataType = '';
  /**
   * Required. Immutable. The source cluster from which Backups will be created
   * via this BackupPlan. Valid formats: - `projects/locations/clusters` -
   * `projects/zones/clusters`
   *
   * @var string
   */
  public $cluster;
  /**
   * Output only. The timestamp when this BackupPlan resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. This flag indicates whether this BackupPlan has been deactivated.
   * Setting this field to True locks the BackupPlan such that no further
   * updates will be allowed (except deletes), including the deactivated field
   * itself. It also prevents any new Backups from being created via this
   * BackupPlan (including scheduled Backups). Default: False
   *
   * @var bool
   */
  public $deactivated;
  /**
   * Optional. User specified descriptive string for this BackupPlan.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. `etag` is used for optimistic concurrency control as a way to
   * help prevent simultaneous updates of a backup plan from overwriting each
   * other. It is strongly suggested that systems make use of the 'etag' in the
   * read-modify-write cycle to perform BackupPlan updates in order to avoid
   * race conditions: An `etag` is returned in the response to `GetBackupPlan`,
   * and systems are expected to put that etag in the request to
   * `UpdateBackupPlan` or `DeleteBackupPlan` to ensure that their change will
   * be applied to the same version of the resource.
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
   * Output only. Completion time of the last successful Backup. This is sourced
   * from a successful Backup's complete_time field. This field is added to
   * maintain consistency with BackupPlanBinding to display last successful
   * backup time.
   *
   * @var string
   */
  public $lastSuccessfulBackupTime;
  /**
   * Output only. Identifier. The full name of the BackupPlan resource. Format:
   * `projects/locations/backupPlans`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The number of Kubernetes Pods backed up in the last successful
   * Backup created via this BackupPlan.
   *
   * @var int
   */
  public $protectedPodCount;
  protected $retentionPolicyType = RetentionPolicy::class;
  protected $retentionPolicyDataType = '';
  /**
   * Output only. A number that represents the current risk level of this
   * BackupPlan from RPO perspective with 1 being no risk and 5 being highest
   * risk.
   *
   * @var int
   */
  public $rpoRiskLevel;
  /**
   * Output only. Human-readable description of why the BackupPlan is in the
   * current rpo_risk_level and action items if any.
   *
   * @var string
   */
  public $rpoRiskReason;
  /**
   * Output only. State of the BackupPlan. This State field reflects the various
   * stages a BackupPlan can be in during the Create operation. It will be set
   * to "DEACTIVATED" if the BackupPlan is deactivated on an Update
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Human-readable description of why BackupPlan is in the current
   * `state`. This field is only meant for human readability and should not be
   * used programmatically as this field is not guaranteed to be consistent.
   *
   * @var string
   */
  public $stateReason;
  /**
   * Output only. Server generated global unique identifier of
   * [UUID](https://en.wikipedia.org/wiki/Universally_unique_identifier) format.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The timestamp when this BackupPlan resource was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The fully qualified name of the BackupChannel to be used to
   * create a backup. This field is set only if the cluster being backed up is
   * in a different project. `projects/locations/backupChannels`
   *
   * @param string $backupChannel
   */
  public function setBackupChannel($backupChannel)
  {
    $this->backupChannel = $backupChannel;
  }
  /**
   * @return string
   */
  public function getBackupChannel()
  {
    return $this->backupChannel;
  }
  /**
   * Optional. Defines the configuration of Backups created via this BackupPlan.
   *
   * @param BackupConfig $backupConfig
   */
  public function setBackupConfig(BackupConfig $backupConfig)
  {
    $this->backupConfig = $backupConfig;
  }
  /**
   * @return BackupConfig
   */
  public function getBackupConfig()
  {
    return $this->backupConfig;
  }
  /**
   * Optional. Defines a schedule for automatic Backup creation via this
   * BackupPlan.
   *
   * @param Schedule $backupSchedule
   */
  public function setBackupSchedule(Schedule $backupSchedule)
  {
    $this->backupSchedule = $backupSchedule;
  }
  /**
   * @return Schedule
   */
  public function getBackupSchedule()
  {
    return $this->backupSchedule;
  }
  /**
   * Required. Immutable. The source cluster from which Backups will be created
   * via this BackupPlan. Valid formats: - `projects/locations/clusters` -
   * `projects/zones/clusters`
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
   * Output only. The timestamp when this BackupPlan resource was created.
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
   * Optional. This flag indicates whether this BackupPlan has been deactivated.
   * Setting this field to True locks the BackupPlan such that no further
   * updates will be allowed (except deletes), including the deactivated field
   * itself. It also prevents any new Backups from being created via this
   * BackupPlan (including scheduled Backups). Default: False
   *
   * @param bool $deactivated
   */
  public function setDeactivated($deactivated)
  {
    $this->deactivated = $deactivated;
  }
  /**
   * @return bool
   */
  public function getDeactivated()
  {
    return $this->deactivated;
  }
  /**
   * Optional. User specified descriptive string for this BackupPlan.
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
   * help prevent simultaneous updates of a backup plan from overwriting each
   * other. It is strongly suggested that systems make use of the 'etag' in the
   * read-modify-write cycle to perform BackupPlan updates in order to avoid
   * race conditions: An `etag` is returned in the response to `GetBackupPlan`,
   * and systems are expected to put that etag in the request to
   * `UpdateBackupPlan` or `DeleteBackupPlan` to ensure that their change will
   * be applied to the same version of the resource.
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
   * Output only. Completion time of the last successful Backup. This is sourced
   * from a successful Backup's complete_time field. This field is added to
   * maintain consistency with BackupPlanBinding to display last successful
   * backup time.
   *
   * @param string $lastSuccessfulBackupTime
   */
  public function setLastSuccessfulBackupTime($lastSuccessfulBackupTime)
  {
    $this->lastSuccessfulBackupTime = $lastSuccessfulBackupTime;
  }
  /**
   * @return string
   */
  public function getLastSuccessfulBackupTime()
  {
    return $this->lastSuccessfulBackupTime;
  }
  /**
   * Output only. Identifier. The full name of the BackupPlan resource. Format:
   * `projects/locations/backupPlans`
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
   * Output only. The number of Kubernetes Pods backed up in the last successful
   * Backup created via this BackupPlan.
   *
   * @param int $protectedPodCount
   */
  public function setProtectedPodCount($protectedPodCount)
  {
    $this->protectedPodCount = $protectedPodCount;
  }
  /**
   * @return int
   */
  public function getProtectedPodCount()
  {
    return $this->protectedPodCount;
  }
  /**
   * Optional. RetentionPolicy governs lifecycle of Backups created under this
   * plan.
   *
   * @param RetentionPolicy $retentionPolicy
   */
  public function setRetentionPolicy(RetentionPolicy $retentionPolicy)
  {
    $this->retentionPolicy = $retentionPolicy;
  }
  /**
   * @return RetentionPolicy
   */
  public function getRetentionPolicy()
  {
    return $this->retentionPolicy;
  }
  /**
   * Output only. A number that represents the current risk level of this
   * BackupPlan from RPO perspective with 1 being no risk and 5 being highest
   * risk.
   *
   * @param int $rpoRiskLevel
   */
  public function setRpoRiskLevel($rpoRiskLevel)
  {
    $this->rpoRiskLevel = $rpoRiskLevel;
  }
  /**
   * @return int
   */
  public function getRpoRiskLevel()
  {
    return $this->rpoRiskLevel;
  }
  /**
   * Output only. Human-readable description of why the BackupPlan is in the
   * current rpo_risk_level and action items if any.
   *
   * @param string $rpoRiskReason
   */
  public function setRpoRiskReason($rpoRiskReason)
  {
    $this->rpoRiskReason = $rpoRiskReason;
  }
  /**
   * @return string
   */
  public function getRpoRiskReason()
  {
    return $this->rpoRiskReason;
  }
  /**
   * Output only. State of the BackupPlan. This State field reflects the various
   * stages a BackupPlan can be in during the Create operation. It will be set
   * to "DEACTIVATED" if the BackupPlan is deactivated on an Update
   *
   * Accepted values: STATE_UNSPECIFIED, CLUSTER_PENDING, PROVISIONING, READY,
   * FAILED, DEACTIVATED, DELETING
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
   * Output only. Human-readable description of why BackupPlan is in the current
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
   * Output only. The timestamp when this BackupPlan resource was last updated.
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
class_alias(BackupPlan::class, 'Google_Service_BackupforGKE_BackupPlan');
