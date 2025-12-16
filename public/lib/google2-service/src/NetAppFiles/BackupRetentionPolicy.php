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

class BackupRetentionPolicy extends \Google\Model
{
  /**
   * Required. Minimum retention duration in days for backups in the backup
   * vault.
   *
   * @var int
   */
  public $backupMinimumEnforcedRetentionDays;
  /**
   * Optional. Indicates if the daily backups are immutable. At least one of
   * daily_backup_immutable, weekly_backup_immutable, monthly_backup_immutable
   * and manual_backup_immutable must be true.
   *
   * @var bool
   */
  public $dailyBackupImmutable;
  /**
   * Optional. Indicates if the manual backups are immutable. At least one of
   * daily_backup_immutable, weekly_backup_immutable, monthly_backup_immutable
   * and manual_backup_immutable must be true.
   *
   * @var bool
   */
  public $manualBackupImmutable;
  /**
   * Optional. Indicates if the monthly backups are immutable. At least one of
   * daily_backup_immutable, weekly_backup_immutable, monthly_backup_immutable
   * and manual_backup_immutable must be true.
   *
   * @var bool
   */
  public $monthlyBackupImmutable;
  /**
   * Optional. Indicates if the weekly backups are immutable. At least one of
   * daily_backup_immutable, weekly_backup_immutable, monthly_backup_immutable
   * and manual_backup_immutable must be true.
   *
   * @var bool
   */
  public $weeklyBackupImmutable;

  /**
   * Required. Minimum retention duration in days for backups in the backup
   * vault.
   *
   * @param int $backupMinimumEnforcedRetentionDays
   */
  public function setBackupMinimumEnforcedRetentionDays($backupMinimumEnforcedRetentionDays)
  {
    $this->backupMinimumEnforcedRetentionDays = $backupMinimumEnforcedRetentionDays;
  }
  /**
   * @return int
   */
  public function getBackupMinimumEnforcedRetentionDays()
  {
    return $this->backupMinimumEnforcedRetentionDays;
  }
  /**
   * Optional. Indicates if the daily backups are immutable. At least one of
   * daily_backup_immutable, weekly_backup_immutable, monthly_backup_immutable
   * and manual_backup_immutable must be true.
   *
   * @param bool $dailyBackupImmutable
   */
  public function setDailyBackupImmutable($dailyBackupImmutable)
  {
    $this->dailyBackupImmutable = $dailyBackupImmutable;
  }
  /**
   * @return bool
   */
  public function getDailyBackupImmutable()
  {
    return $this->dailyBackupImmutable;
  }
  /**
   * Optional. Indicates if the manual backups are immutable. At least one of
   * daily_backup_immutable, weekly_backup_immutable, monthly_backup_immutable
   * and manual_backup_immutable must be true.
   *
   * @param bool $manualBackupImmutable
   */
  public function setManualBackupImmutable($manualBackupImmutable)
  {
    $this->manualBackupImmutable = $manualBackupImmutable;
  }
  /**
   * @return bool
   */
  public function getManualBackupImmutable()
  {
    return $this->manualBackupImmutable;
  }
  /**
   * Optional. Indicates if the monthly backups are immutable. At least one of
   * daily_backup_immutable, weekly_backup_immutable, monthly_backup_immutable
   * and manual_backup_immutable must be true.
   *
   * @param bool $monthlyBackupImmutable
   */
  public function setMonthlyBackupImmutable($monthlyBackupImmutable)
  {
    $this->monthlyBackupImmutable = $monthlyBackupImmutable;
  }
  /**
   * @return bool
   */
  public function getMonthlyBackupImmutable()
  {
    return $this->monthlyBackupImmutable;
  }
  /**
   * Optional. Indicates if the weekly backups are immutable. At least one of
   * daily_backup_immutable, weekly_backup_immutable, monthly_backup_immutable
   * and manual_backup_immutable must be true.
   *
   * @param bool $weeklyBackupImmutable
   */
  public function setWeeklyBackupImmutable($weeklyBackupImmutable)
  {
    $this->weeklyBackupImmutable = $weeklyBackupImmutable;
  }
  /**
   * @return bool
   */
  public function getWeeklyBackupImmutable()
  {
    return $this->weeklyBackupImmutable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupRetentionPolicy::class, 'Google_Service_NetAppFiles_BackupRetentionPolicy');
