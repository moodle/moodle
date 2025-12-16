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

class Schedule extends \Google\Model
{
  /**
   * Optional. A standard [cron](https://wikipedia.com/wiki/cron) string that
   * defines a repeating schedule for creating Backups via this BackupPlan. This
   * is mutually exclusive with the rpo_config field since at most one schedule
   * can be defined for a BackupPlan. If this is defined, then
   * backup_retain_days must also be defined. Default (empty): no automatic
   * backup creation will occur.
   *
   * @var string
   */
  public $cronSchedule;
  /**
   * Output only. Start time of next scheduled backup under this BackupPlan by
   * either cron_schedule or rpo config.
   *
   * @var string
   */
  public $nextScheduledBackupTime;
  /**
   * Optional. This flag denotes whether automatic Backup creation is paused for
   * this BackupPlan. Default: False
   *
   * @var bool
   */
  public $paused;
  protected $rpoConfigType = RpoConfig::class;
  protected $rpoConfigDataType = '';

  /**
   * Optional. A standard [cron](https://wikipedia.com/wiki/cron) string that
   * defines a repeating schedule for creating Backups via this BackupPlan. This
   * is mutually exclusive with the rpo_config field since at most one schedule
   * can be defined for a BackupPlan. If this is defined, then
   * backup_retain_days must also be defined. Default (empty): no automatic
   * backup creation will occur.
   *
   * @param string $cronSchedule
   */
  public function setCronSchedule($cronSchedule)
  {
    $this->cronSchedule = $cronSchedule;
  }
  /**
   * @return string
   */
  public function getCronSchedule()
  {
    return $this->cronSchedule;
  }
  /**
   * Output only. Start time of next scheduled backup under this BackupPlan by
   * either cron_schedule or rpo config.
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
   * Optional. This flag denotes whether automatic Backup creation is paused for
   * this BackupPlan. Default: False
   *
   * @param bool $paused
   */
  public function setPaused($paused)
  {
    $this->paused = $paused;
  }
  /**
   * @return bool
   */
  public function getPaused()
  {
    return $this->paused;
  }
  /**
   * Optional. Defines the RPO schedule configuration for this BackupPlan. This
   * is mutually exclusive with the cron_schedule field since at most one
   * schedule can be defined for a BackupPLan. If this is defined, then
   * backup_retain_days must also be defined. Default (empty): no automatic
   * backup creation will occur.
   *
   * @param RpoConfig $rpoConfig
   */
  public function setRpoConfig(RpoConfig $rpoConfig)
  {
    $this->rpoConfig = $rpoConfig;
  }
  /**
   * @return RpoConfig
   */
  public function getRpoConfig()
  {
    return $this->rpoConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Schedule::class, 'Google_Service_BackupforGKE_Schedule');
