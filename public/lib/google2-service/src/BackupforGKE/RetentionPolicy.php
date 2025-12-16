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

class RetentionPolicy extends \Google\Model
{
  /**
   * Optional. Minimum age for Backups created via this BackupPlan (in days).
   * This field MUST be an integer value between 0-90 (inclusive). A Backup
   * created under this BackupPlan will NOT be deletable until it reaches
   * Backup's (create_time + backup_delete_lock_days). Updating this field of a
   * BackupPlan does NOT affect existing Backups under it. Backups created AFTER
   * a successful update will inherit the new value. Default: 0 (no delete
   * blocking)
   *
   * @var int
   */
  public $backupDeleteLockDays;
  /**
   * Optional. The default maximum age of a Backup created via this BackupPlan.
   * This field MUST be an integer value >= 0 and <= 365. If specified, a Backup
   * created under this BackupPlan will be automatically deleted after its age
   * reaches (create_time + backup_retain_days). If not specified, Backups
   * created under this BackupPlan will NOT be subject to automatic deletion.
   * Updating this field does NOT affect existing Backups under it. Backups
   * created AFTER a successful update will automatically pick up the new value.
   * NOTE: backup_retain_days must be >= backup_delete_lock_days. If
   * cron_schedule is defined, then this must be <= 360 * the creation interval.
   * If rpo_config is defined, then this must be <= 360 * target_rpo_minutes /
   * (1440minutes/day). Default: 0 (no automatic deletion)
   *
   * @var int
   */
  public $backupRetainDays;
  /**
   * Optional. This flag denotes whether the retention policy of this BackupPlan
   * is locked. If set to True, no further update is allowed on this policy,
   * including the `locked` field itself. Default: False
   *
   * @var bool
   */
  public $locked;

  /**
   * Optional. Minimum age for Backups created via this BackupPlan (in days).
   * This field MUST be an integer value between 0-90 (inclusive). A Backup
   * created under this BackupPlan will NOT be deletable until it reaches
   * Backup's (create_time + backup_delete_lock_days). Updating this field of a
   * BackupPlan does NOT affect existing Backups under it. Backups created AFTER
   * a successful update will inherit the new value. Default: 0 (no delete
   * blocking)
   *
   * @param int $backupDeleteLockDays
   */
  public function setBackupDeleteLockDays($backupDeleteLockDays)
  {
    $this->backupDeleteLockDays = $backupDeleteLockDays;
  }
  /**
   * @return int
   */
  public function getBackupDeleteLockDays()
  {
    return $this->backupDeleteLockDays;
  }
  /**
   * Optional. The default maximum age of a Backup created via this BackupPlan.
   * This field MUST be an integer value >= 0 and <= 365. If specified, a Backup
   * created under this BackupPlan will be automatically deleted after its age
   * reaches (create_time + backup_retain_days). If not specified, Backups
   * created under this BackupPlan will NOT be subject to automatic deletion.
   * Updating this field does NOT affect existing Backups under it. Backups
   * created AFTER a successful update will automatically pick up the new value.
   * NOTE: backup_retain_days must be >= backup_delete_lock_days. If
   * cron_schedule is defined, then this must be <= 360 * the creation interval.
   * If rpo_config is defined, then this must be <= 360 * target_rpo_minutes /
   * (1440minutes/day). Default: 0 (no automatic deletion)
   *
   * @param int $backupRetainDays
   */
  public function setBackupRetainDays($backupRetainDays)
  {
    $this->backupRetainDays = $backupRetainDays;
  }
  /**
   * @return int
   */
  public function getBackupRetainDays()
  {
    return $this->backupRetainDays;
  }
  /**
   * Optional. This flag denotes whether the retention policy of this BackupPlan
   * is locked. If set to True, no further update is allowed on this policy,
   * including the `locked` field itself. Default: False
   *
   * @param bool $locked
   */
  public function setLocked($locked)
  {
    $this->locked = $locked;
  }
  /**
   * @return bool
   */
  public function getLocked()
  {
    return $this->locked;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RetentionPolicy::class, 'Google_Service_BackupforGKE_RetentionPolicy');
