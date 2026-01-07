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

class BackupConfig extends \Google\Collection
{
  protected $collection_key = 'backupPolicies';
  /**
   * Output only. Total size of all backups in a chain in bytes = baseline
   * backup size + sum(incremental backup size).
   *
   * @var string
   */
  public $backupChainBytes;
  /**
   * Optional. When specified, schedule backups will be created based on the
   * policy configuration.
   *
   * @var string[]
   */
  public $backupPolicies;
  /**
   * Optional. Name of backup vault. Format:
   * projects/{project_id}/locations/{location}/backupVaults/{backup_vault_id}
   *
   * @var string
   */
  public $backupVault;
  /**
   * Optional. When set to true, scheduled backup is enabled on the volume. This
   * field should be nil when there's no backup policy attached.
   *
   * @var bool
   */
  public $scheduledBackupEnabled;

  /**
   * Output only. Total size of all backups in a chain in bytes = baseline
   * backup size + sum(incremental backup size).
   *
   * @param string $backupChainBytes
   */
  public function setBackupChainBytes($backupChainBytes)
  {
    $this->backupChainBytes = $backupChainBytes;
  }
  /**
   * @return string
   */
  public function getBackupChainBytes()
  {
    return $this->backupChainBytes;
  }
  /**
   * Optional. When specified, schedule backups will be created based on the
   * policy configuration.
   *
   * @param string[] $backupPolicies
   */
  public function setBackupPolicies($backupPolicies)
  {
    $this->backupPolicies = $backupPolicies;
  }
  /**
   * @return string[]
   */
  public function getBackupPolicies()
  {
    return $this->backupPolicies;
  }
  /**
   * Optional. Name of backup vault. Format:
   * projects/{project_id}/locations/{location}/backupVaults/{backup_vault_id}
   *
   * @param string $backupVault
   */
  public function setBackupVault($backupVault)
  {
    $this->backupVault = $backupVault;
  }
  /**
   * @return string
   */
  public function getBackupVault()
  {
    return $this->backupVault;
  }
  /**
   * Optional. When set to true, scheduled backup is enabled on the volume. This
   * field should be nil when there's no backup policy attached.
   *
   * @param bool $scheduledBackupEnabled
   */
  public function setScheduledBackupEnabled($scheduledBackupEnabled)
  {
    $this->scheduledBackupEnabled = $scheduledBackupEnabled;
  }
  /**
   * @return bool
   */
  public function getScheduledBackupEnabled()
  {
    return $this->scheduledBackupEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupConfig::class, 'Google_Service_NetAppFiles_BackupConfig');
