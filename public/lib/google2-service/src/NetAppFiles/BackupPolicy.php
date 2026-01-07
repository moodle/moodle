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

namespace Google\Service\NetAppFiles;

class BackupPolicy extends \Google\Model
{
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * BackupPolicy is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * BackupPolicy is available for use.
   */
  public const STATE_READY = 'READY';
  /**
   * BackupPolicy is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * BackupPolicy is not valid and cannot be used.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * BackupPolicy is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Output only. The total number of volumes assigned by this backup policy.
   *
   * @var int
   */
  public $assignedVolumeCount;
  /**
   * Output only. The time when the backup policy was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Number of daily backups to keep. Note that the minimum daily backup limit
   * is 2.
   *
   * @var int
   */
  public $dailyBackupLimit;
  /**
   * Description of the backup policy.
   *
   * @var string
   */
  public $description;
  /**
   * If enabled, make backups automatically according to the schedules. This
   * will be applied to all volumes that have this policy attached and enforced
   * on volume level. If not specified, default is true.
   *
   * @var bool
   */
  public $enabled;
  /**
   * Resource labels to represent user provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Number of monthly backups to keep. Note that the sum of daily, weekly and
   * monthly backups should be greater than 1.
   *
   * @var int
   */
  public $monthlyBackupLimit;
  /**
   * Identifier. The resource name of the backup policy. Format: `projects/{proj
   * ect_id}/locations/{location}/backupPolicies/{backup_policy_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The backup policy state.
   *
   * @var string
   */
  public $state;
  /**
   * Number of weekly backups to keep. Note that the sum of daily, weekly and
   * monthly backups should be greater than 1.
   *
   * @var int
   */
  public $weeklyBackupLimit;

  /**
   * Output only. The total number of volumes assigned by this backup policy.
   *
   * @param int $assignedVolumeCount
   */
  public function setAssignedVolumeCount($assignedVolumeCount)
  {
    $this->assignedVolumeCount = $assignedVolumeCount;
  }
  /**
   * @return int
   */
  public function getAssignedVolumeCount()
  {
    return $this->assignedVolumeCount;
  }
  /**
   * Output only. The time when the backup policy was created.
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
   * Number of daily backups to keep. Note that the minimum daily backup limit
   * is 2.
   *
   * @param int $dailyBackupLimit
   */
  public function setDailyBackupLimit($dailyBackupLimit)
  {
    $this->dailyBackupLimit = $dailyBackupLimit;
  }
  /**
   * @return int
   */
  public function getDailyBackupLimit()
  {
    return $this->dailyBackupLimit;
  }
  /**
   * Description of the backup policy.
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
   * If enabled, make backups automatically according to the schedules. This
   * will be applied to all volumes that have this policy attached and enforced
   * on volume level. If not specified, default is true.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * Resource labels to represent user provided metadata.
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
   * Number of monthly backups to keep. Note that the sum of daily, weekly and
   * monthly backups should be greater than 1.
   *
   * @param int $monthlyBackupLimit
   */
  public function setMonthlyBackupLimit($monthlyBackupLimit)
  {
    $this->monthlyBackupLimit = $monthlyBackupLimit;
  }
  /**
   * @return int
   */
  public function getMonthlyBackupLimit()
  {
    return $this->monthlyBackupLimit;
  }
  /**
   * Identifier. The resource name of the backup policy. Format: `projects/{proj
   * ect_id}/locations/{location}/backupPolicies/{backup_policy_id}`.
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
   * Output only. The backup policy state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, DELETING, ERROR,
   * UPDATING
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
   * Number of weekly backups to keep. Note that the sum of daily, weekly and
   * monthly backups should be greater than 1.
   *
   * @param int $weeklyBackupLimit
   */
  public function setWeeklyBackupLimit($weeklyBackupLimit)
  {
    $this->weeklyBackupLimit = $weeklyBackupLimit;
  }
  /**
   * @return int
   */
  public function getWeeklyBackupLimit()
  {
    return $this->weeklyBackupLimit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupPolicy::class, 'Google_Service_NetAppFiles_BackupPolicy');
