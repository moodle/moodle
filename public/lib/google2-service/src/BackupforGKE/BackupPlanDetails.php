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

class BackupPlanDetails extends \Google\Model
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
  protected $backupConfigDetailsType = BackupConfigDetails::class;
  protected $backupConfigDetailsDataType = '';
  /**
   * Output only. The fully qualified name of the last successful Backup created
   * under this BackupPlan. `projects/locations/backupPlans/backups`
   *
   * @var string
   */
  public $lastSuccessfulBackup;
  /**
   * Output only. Completion time of the last successful Backup. This is sourced
   * from a successful Backup's complete_time field.
   *
   * @var string
   */
  public $lastSuccessfulBackupTime;
  /**
   * Output only. Start time of next scheduled backup under this BackupPlan by
   * either cron_schedule or rpo config. This is sourced from BackupPlan.
   *
   * @var string
   */
  public $nextScheduledBackupTime;
  /**
   * Output only. The number of Kubernetes Pods backed up in the last successful
   * Backup created via this BackupPlan.
   *
   * @var int
   */
  public $protectedPodCount;
  protected $retentionPolicyDetailsType = RetentionPolicyDetails::class;
  protected $retentionPolicyDetailsDataType = '';
  /**
   * Output only. A number that represents the current risk level of this
   * BackupPlan from RPO perspective with 1 being no risk and 5 being highest
   * risk.
   *
   * @var int
   */
  public $rpoRiskLevel;
  /**
   * Output only. State of the BackupPlan.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Contains details about the BackupConfig of Backups created via
   * this BackupPlan.
   *
   * @param BackupConfigDetails $backupConfigDetails
   */
  public function setBackupConfigDetails(BackupConfigDetails $backupConfigDetails)
  {
    $this->backupConfigDetails = $backupConfigDetails;
  }
  /**
   * @return BackupConfigDetails
   */
  public function getBackupConfigDetails()
  {
    return $this->backupConfigDetails;
  }
  /**
   * Output only. The fully qualified name of the last successful Backup created
   * under this BackupPlan. `projects/locations/backupPlans/backups`
   *
   * @param string $lastSuccessfulBackup
   */
  public function setLastSuccessfulBackup($lastSuccessfulBackup)
  {
    $this->lastSuccessfulBackup = $lastSuccessfulBackup;
  }
  /**
   * @return string
   */
  public function getLastSuccessfulBackup()
  {
    return $this->lastSuccessfulBackup;
  }
  /**
   * Output only. Completion time of the last successful Backup. This is sourced
   * from a successful Backup's complete_time field.
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
   * Output only. Start time of next scheduled backup under this BackupPlan by
   * either cron_schedule or rpo config. This is sourced from BackupPlan.
   *
   * @param string $nextScheduledBackupTime
   */
  public function setNextScheduledBackupTime($nextScheduledBackupTime)
  {
    $this->nextScheduledBackupTime = $nextScheduledBackupTime;
  }
  /**
   * @return string
   */
  public function getNextScheduledBackupTime()
  {
    return $this->nextScheduledBackupTime;
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
   * Output only. Contains details about the RetentionPolicy of Backups created
   * via this BackupPlan.
   *
   * @param RetentionPolicyDetails $retentionPolicyDetails
   */
  public function setRetentionPolicyDetails(RetentionPolicyDetails $retentionPolicyDetails)
  {
    $this->retentionPolicyDetails = $retentionPolicyDetails;
  }
  /**
   * @return RetentionPolicyDetails
   */
  public function getRetentionPolicyDetails()
  {
    return $this->retentionPolicyDetails;
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
   * Output only. State of the BackupPlan.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupPlanDetails::class, 'Google_Service_BackupforGKE_BackupPlanDetails');
