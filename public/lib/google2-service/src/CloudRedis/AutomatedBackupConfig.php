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

namespace Google\Service\CloudRedis;

class AutomatedBackupConfig extends \Google\Model
{
  /**
   * Default value. Automated backup config is not specified.
   */
  public const AUTOMATED_BACKUP_MODE_AUTOMATED_BACKUP_MODE_UNSPECIFIED = 'AUTOMATED_BACKUP_MODE_UNSPECIFIED';
  /**
   * Automated backup config disabled.
   */
  public const AUTOMATED_BACKUP_MODE_DISABLED = 'DISABLED';
  /**
   * Automated backup config enabled.
   */
  public const AUTOMATED_BACKUP_MODE_ENABLED = 'ENABLED';
  /**
   * Optional. The automated backup mode. If the mode is disabled, the other
   * fields will be ignored.
   *
   * @var string
   */
  public $automatedBackupMode;
  protected $fixedFrequencyScheduleType = FixedFrequencySchedule::class;
  protected $fixedFrequencyScheduleDataType = '';
  /**
   * Optional. How long to keep automated backups before the backups are
   * deleted. The value should be between 1 day and 365 days. If not specified,
   * the default value is 35 days.
   *
   * @var string
   */
  public $retention;

  /**
   * Optional. The automated backup mode. If the mode is disabled, the other
   * fields will be ignored.
   *
   * Accepted values: AUTOMATED_BACKUP_MODE_UNSPECIFIED, DISABLED, ENABLED
   *
   * @param self::AUTOMATED_BACKUP_MODE_* $automatedBackupMode
   */
  public function setAutomatedBackupMode($automatedBackupMode)
  {
    $this->automatedBackupMode = $automatedBackupMode;
  }
  /**
   * @return self::AUTOMATED_BACKUP_MODE_*
   */
  public function getAutomatedBackupMode()
  {
    return $this->automatedBackupMode;
  }
  /**
   * Optional. Trigger automated backups at a fixed frequency.
   *
   * @param FixedFrequencySchedule $fixedFrequencySchedule
   */
  public function setFixedFrequencySchedule(FixedFrequencySchedule $fixedFrequencySchedule)
  {
    $this->fixedFrequencySchedule = $fixedFrequencySchedule;
  }
  /**
   * @return FixedFrequencySchedule
   */
  public function getFixedFrequencySchedule()
  {
    return $this->fixedFrequencySchedule;
  }
  /**
   * Optional. How long to keep automated backups before the backups are
   * deleted. The value should be between 1 day and 365 days. If not specified,
   * the default value is 35 days.
   *
   * @param string $retention
   */
  public function setRetention($retention)
  {
    $this->retention = $retention;
  }
  /**
   * @return string
   */
  public function getRetention()
  {
    return $this->retention;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutomatedBackupConfig::class, 'Google_Service_CloudRedis_AutomatedBackupConfig');
