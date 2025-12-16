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

class BackupConfiguration extends \Google\Model
{
  /**
   * Whether customer visible automated backups are enabled on the instance.
   *
   * @var bool
   */
  public $automatedBackupEnabled;
  protected $backupRetentionSettingsType = RetentionSettings::class;
  protected $backupRetentionSettingsDataType = '';
  /**
   * Whether point-in-time recovery is enabled. This is optional field, if the
   * database service does not have this feature or metadata is not available in
   * control plane, this can be omitted.
   *
   * @var bool
   */
  public $pointInTimeRecoveryEnabled;

  /**
   * Whether customer visible automated backups are enabled on the instance.
   *
   * @param bool $automatedBackupEnabled
   */
  public function setAutomatedBackupEnabled($automatedBackupEnabled)
  {
    $this->automatedBackupEnabled = $automatedBackupEnabled;
  }
  /**
   * @return bool
   */
  public function getAutomatedBackupEnabled()
  {
    return $this->automatedBackupEnabled;
  }
  /**
   * Backup retention settings.
   *
   * @param RetentionSettings $backupRetentionSettings
   */
  public function setBackupRetentionSettings(RetentionSettings $backupRetentionSettings)
  {
    $this->backupRetentionSettings = $backupRetentionSettings;
  }
  /**
   * @return RetentionSettings
   */
  public function getBackupRetentionSettings()
  {
    return $this->backupRetentionSettings;
  }
  /**
   * Whether point-in-time recovery is enabled. This is optional field, if the
   * database service does not have this feature or metadata is not available in
   * control plane, this can be omitted.
   *
   * @param bool $pointInTimeRecoveryEnabled
   */
  public function setPointInTimeRecoveryEnabled($pointInTimeRecoveryEnabled)
  {
    $this->pointInTimeRecoveryEnabled = $pointInTimeRecoveryEnabled;
  }
  /**
   * @return bool
   */
  public function getPointInTimeRecoveryEnabled()
  {
    return $this->pointInTimeRecoveryEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupConfiguration::class, 'Google_Service_CloudRedis_BackupConfiguration');
